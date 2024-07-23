<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class MasterUnitController extends BaseController
{
    function __construct()
    {
        $this->unit = new Unit();
    }

    public function index()
    {
        $title = 'Master Satuan';
        $js = 'js/apps/master/unit.js?_='.rand();
        return view('master.unit', compact('js', 'title'));
    }

    public function datatable_unit(Request $request)
    {
        $data = $this->unit->dataTableUnit();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function($row) {
                $btn = '';
                if(Gate::allows('crudAccess', 'MD3', $row)) {
                    if($row->status == 1) {
                        $btn = '<a class="btn btn-dim btn-outline-secondary btn-sm" onclick="edit(\'' . $row->uid . '\')"><em class="icon ni ni-edit"></em><span>Edit</span></a>
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                            ';
                    }
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store_unit(Request $request)
    {
        $uid = $request->input('uid');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());        
        }

        $user = Auth::user();

        $data = [
            'name' => $request->name
        ];

        if(!empty($uid)) {
            $data['update_at'] = Carbon::now();
            $data['update_by'] = $user->id;
        }else{
            $data['insert_at'] = Carbon::now();
            $data['insert_by'] = $user->id;
            $uid_unit = 'U'.Carbon::now()->format('YmdHisu');
            $data['uid'] = $uid_unit;
        }

        $process = DB::table('unit')->updateOrInsert(
            ['uid' => $uid],
            $data
        );

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function edit_unit(Request $request) 
    {
        $uid = $request->uid;
        $data = Unit::where('uid', $uid)->first();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_unit(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('unit')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function list_data_unit(Request $request)
    {
        $q = $request->get('q');
        $data = $this->unit->listDataUnit($q);
        return response()->json($data);
    }
}
