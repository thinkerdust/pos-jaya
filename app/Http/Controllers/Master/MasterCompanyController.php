<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MasterCompanyController extends BaseController
{
    function __construct()
    {
        $this->company = new Company();
    }

    public function index()
    {
        $title = 'Master Perusahaan';
        $js = 'js/apps/master/company.js?_='.rand();
        return view('master.company', compact('title', 'js'));
    }

    public function datatable_company(Request $request)
    {
        $data = $this->company->dataTableCompany();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function($row) {
                $btn = '';
                if(Gate::allows('crudAccess', 'MD1', $row)) {
                    if($row->status == 1) {
                        $btn = '<a href="/company/add?uid='.$row->uid.'" class="btn btn-dim btn-outline-secondary btn-sm"><em class="icon ni ni-edit"></em><span>Edit</span></a>&nbsp;
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus('.$row->uid.')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                            ';
                    }
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function add_company(Request $request)
    {
        $uid = $request->uid;
        $js = 'js/apps/master/add-company.js?_='.rand();
        return view('master.add_company', compact('js', 'uid'));
    }

    public function store_company(Request $request)
    {
        $uid = $request->input('uid');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'photo' => 'required_if:uid,null|file|mimes:jpg,png,jpeg,gif,svg,pdf',
        ]);

        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());        
        }

        DB::beginTransaction();
        try {

            $user = Auth::user();

            $data = [
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ];

            if(!empty($uid)) {
                $data['update_at'] = Carbon::now();
                $data['update_by'] = $user->id;
            }else{
                $data['insert_at'] = Carbon::now();
                $data['insert_by'] = $user->id;
                $uid_company = 'C'.Carbon::now()->format('YmdHisu');
                $data['uid'] = $uid_company;
            }

            // remove old photo
            if(!empty($uid) && $request->file('photo')) {
                $data_company = Company::where('uid', $uid)->first();
                $oldFile = $data_company->photo;

                if(!empty($oldFile)) {
                    if (Storage::disk('public')->exists($oldFile)) {
                        // Delete the file
                        Storage::disk('public')->delete($oldFile);
                    }
                }
                
            }

            // upload photo
            if($request->file('photo')) {

                $file = $request->file('photo');
                $fileName = $file->getClientOriginalName();
                $fileName = str_replace(' ', '', $fileName);

                // Define a file path
                $filePath = 'uploads/company/photo/' . uniqid() . '_' . $fileName;

                // Store the file in the local storage
                $upload = Storage::disk('public')->put($filePath, file_get_contents($file));
                if ($upload) {
                    $data['photo'] = $filePath;
                } 
            }

            DB::table('company')->updateOrInsert(
                ['uid' => $uid],
                $data
            );

            DB::commit();
            return $this->ajaxResponse(true, 'Data save successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->ajaxResponse(false, 'Failed to save data', $e);
        }
    }

    public function edit_company(Request $request)
    {
        $uid = $request->uid;
        $data = Company::where('uid', $uid)->first();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_company(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('company')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function list_data_company(Request $request)
    {
        $q = $request->get('q');
        $data = $this->company->listDataCompany($q);
        return response()->json($data);
    }
}
