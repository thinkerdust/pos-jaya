<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\ProductCategories;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class ProductCategoriesController extends BaseController
{
    function __construct()
    {
        $this->product_categories = new ProductCategories();
    }

    public function index()
    {
        $title = 'Master Produk Kategori';
        $js = 'js/apps/master/product-categories.js?_='.rand();
        return view('master.product_categories', compact('js', 'title'));
    }

    public function datatable_product_categories(Request $request)
    {
        $data = $this->product_categories->dataTableProductCategories();
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

    public function store_product_categories(Request $request)
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
            $uid_product_categories = 'C'.Carbon::now()->format('YmdHisu');
            $data['uid'] = $uid_product_categories;
        }

        $process = DB::table('product_categories')->updateOrInsert(
            ['uid' => $uid],
            $data
        );

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function edit_product_categories(Request $request) 
    {
        $uid = $request->uid;
        $data = ProductCategories::where('uid', $uid)->first();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_product_categories(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('product_categories')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        if($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        }else{
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function list_data_product_categories(Request $request)
    {
        $q = $request->get('q');
        $data = $this->product_categories->listDataProductCategories($q);
        return response()->json($data);
    }
}
