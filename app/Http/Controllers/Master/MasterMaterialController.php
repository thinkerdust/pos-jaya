<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Models\Material;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class MasterMaterialController extends BaseController
{
    function __construct()
    {
        $this->material = new Material();
    }

    public function index()
    {
        $title = 'Master Bahan';
        $js = 'js/apps/master/material.js?_='.rand();
        return view('master.material', compact('js', 'title'));
    }

    public function datatable_material(Request $request)
    {
        $data = $this->material->dataTableMaterial();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function($row) {
                $btn = '';
                if(Gate::allows('crudAccess', 'MD6', $row)) {
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

    public function store_material(Request $request)
    {
        $uid = $request->input('uid');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'unit' => 'required',
            'volume' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'supplier' => 'required',
            'length' => 'required',
            'wide' => 'required',
        ]);

        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());        
        }

        $user = Auth::user();

        $volume = Str::replace('.', '', $request->volume);
        $price = Str::replace('.', '', $request->price);
        $stock = Str::replace('.', '', $request->stock);
        $length = Str::replace('.', '', $request->length);
        $wide = Str::replace('.', '', $request->wide);

        $data = [
            'name' => $request->name,
            'uid_unit' => $request->unit,
            'volume' => $volume,
            'price' => $price,
            'stock' => $stock,
            'uid_supplier' => $request->supplier,
            'length' => $length,
            'wide' => $wide,
        ];

        if(!empty($uid)) {
            $data['update_at'] = Carbon::now();
            $data['update_by'] = $user->id;
        }else{
            $data['insert_at'] = Carbon::now();
            $data['insert_by'] = $user->id;
            $uid_material = 'M'.Carbon::now()->format('YmdHisu');
            $data['uid'] = $uid_material;
        }

        $process = DB::table('material')->updateOrInsert(
            ['uid' => $uid],
            $data
        );

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function edit_material(Request $request) 
    {
        $uid = $request->uid;
        $data = $this->material->editMaterial($uid);
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_material(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('material')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function list_data_material(Request $request)
    {
        $q = $request->get('q');
        $data = $this->material->listDataMaterial($q);
        return response()->json($data);
    }
}
