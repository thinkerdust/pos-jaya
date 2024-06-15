<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class MasterProductController extends BaseController
{
    function __construct()
    {
        $this->product = new Product();
    }

    public function index()
    {
        $title = 'Master Produk';
        $js = 'js/apps/master/product.js?_='.rand();
        return view('master.product', compact('js', 'title'));
    }

    public function datatable_product(Request $request)
    {
        $data = $this->product->dataTableProduct();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function($row) {
                $btn = '';
                if(Gate::allows('crudAccess', 'MD3', $row)) {
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

    public function store_product(Request $request)
    {
        $uid = $request->input('uid');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'material' => 'required',
            'unit' => 'required'
        ]);

        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());        
        }

        $user = Auth::user();

        $data = [
            'name' => $request->name,
            'uid_material' => $request->material,
            'uid_unit' => $request->unit
        ];

        if(!empty($uid)) {
            $data['update_at'] = Carbon::now();
            $data['update_by'] = $user->username;
        }else{
            $data['insert_at'] = Carbon::now();
            $data['insert_by'] = $user->username;
            $uid_product = 'P'.Carbon::now()->format('YmdHisu');
            $data['uid'] = $uid_product;
        }

        $process = DB::table('product')->updateOrInsert(
            ['uid' => $uid],
            $data
        );

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function edit_product(Request $request) 
    {
        $uid = $request->uid;
        $data = $this->product->editProduct($uid);
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_product(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('product')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->username]);

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function list_data_product(Request $request)
    {
        $q = $request->get('q');
        $data = $this->product->listDataProduct($q);
        return response()->json($data);
    }
}
