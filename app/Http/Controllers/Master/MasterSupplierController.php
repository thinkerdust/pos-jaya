<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class MasterSupplierController extends BaseController
{
    function __construct()
    {
        $this->supplier = new Supplier();
    }

    public function index()
    {
        $title = 'Master Supplier';
        $js = 'js/apps/master/supplier.js?_='.rand();
        return view('master.supplier', compact('js', 'title'));
    }

    public function datatable_supplier(Request $request)
    {
        $data = $this->supplier->dataTableSupplier();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function($row) {
                $btn = '';
                if(Gate::allows('crudAccess', 'MD7', $row)) {
                    if($row->status == 1) {
                        $btn = '<a class="btn btn-dim btn-outline-secondary btn-sm" onclick="edit(\'' . $row->uid . '\')"><em class="icon ni ni-edit"></em><span>Edit</span></a>&nbsp;
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                            ';
                    }
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store_supplier(Request $request)
    {
        $uid = $request->input('uid');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email:rfc,dns',
            'address' => 'required',
        ]);

        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());        
        }

        $user = Auth::user();

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ];

        if(!empty($uid)) {
            $data['update_at'] = Carbon::now();
            $data['update_by'] = $user->username;
        }else{
            $data['insert_at'] = Carbon::now();
            $data['insert_by'] = $user->username;
            $uid_supplier = 'S'.Carbon::now()->format('YmdHisu');
            $data['uid'] = $uid_supplier;
        }

        $process = DB::table('supplier')->updateOrInsert(
            ['uid' => $uid],
            $data
        );

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function edit_supplier(Request $request) 
    {
        $uid = $request->uid;
        $data = Supplier::where('uid', $uid)->first();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_supplier(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('supplier')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->username]);

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function list_data_supplier(Request $request)
    {
        $q = $request->get('q');
        $data = $this->supplier->listDataSupplier($q);
        return response()->json($data);
    }
}
