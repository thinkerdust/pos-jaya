<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
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
        $js = 'js/apps/master/product.js?_=' . rand();
        return view('master.product', compact('js', 'title'));
    }

    public function datatable_product(Request $request)
    {
        $data = $this->product->dataTableProduct();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                if (Gate::allows('crudAccess', 'MD3', $row)) {
                    if ($row->status == 1) {
                        $btn = '<a class="btn btn-dim btn-outline-secondary btn-sm" onclick="edit(\'' . $row->uid . '\')"><em class="icon ni ni-edit"></em><span>Edit</span></a>
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="addprice(\'' . $row->uid . '\')"><em class="icon ni ni-coin-alt"></em><span>Harga Grosir</span></a>
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
            'product_categories' => 'required',
            'unit' => 'required',
            'cost_price' => 'required',
            'sell_price' => 'required',
            'retail_member_price' => 'required',
            'description' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->ajaxResponse(false, $validator->errors()->first());
        }

        $user = Auth::user();

        $cost_price = Str::replace('.', '', $request->cost_price);
        $sell_price = Str::replace('.', '', $request->sell_price);
        $retail_member_price = Str::replace('.', '', $request->retail_member_price);

        $data = [
            'name' => $request->name,
            'uid_product_categories' => $request->product_categories,
            'uid_unit' => $request->unit,
            'cost_price' => $cost_price,
            'sell_price' => $sell_price,
            'retail_member_price' => $retail_member_price,
            'description' => $request->description,
            'uid_company' => $user->uid_company
        ];

        if (!empty($uid)) {
            $data['update_at'] = Carbon::now();
            $data['update_by'] = $user->id;
        } else {
            $data['insert_at'] = Carbon::now();
            $data['insert_by'] = $user->id;
            $uid_product = 'P' . Carbon::now()->format('YmdHisu');
            $data['uid'] = $uid_product;
        }

        $process = DB::table('product')->updateOrInsert(
            ['uid' => $uid],
            $data
        );

        if ($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
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
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        if ($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function list_data_product(Request $request)
    {
        $q = $request->get('q');
        $data = $this->product->listDataProduct($q);
        return response()->json($data);
    }

    public function datatable_product_price(Request $request)
    {
        $uid_product = $request->uid_product;
        $data = $this->product->dataTableProductPrice($uid_product);
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                if (Gate::allows('crudAccess', 'MD3', $row)) {
                    if ($row->status == 1) {
                        $btn = '<a class="btn btn-dim btn-outline-secondary btn-sm" onclick="edit_price(\'' . $row->uid . '\')"><em class="icon ni ni-edit"></em><span>Edit</span></a>&nbsp;
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus_price(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                            ';
                    }
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store_product_price(Request $request)
    {
        $uid = $request->input('uid_price');

        $validator = Validator::make($request->all(), [
            'first_quantity' => 'required',
            'last_quantity' => 'required',
            'price' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->ajaxResponse(false, $validator->errors()->first());
        }

        $user = Auth::user();

        $first_quantity = Str::replace('.', '', $request->first_quantity);
        $last_quantity = Str::replace('.', '', $request->last_quantity);
        $price = Str::replace('.', '', $request->price);

        $data = [
            'uid_product' => $request->uid_product,
            'first_quantity' => $first_quantity,
            'last_quantity' => $last_quantity,
            'price' => $price,
        ];

        if (!empty($uid)) {
            $data['update_at'] = Carbon::now();
            $data['update_by'] = $user->id;
        } else {
            $data['insert_at'] = Carbon::now();
            $data['insert_by'] = $user->id;
            $uid_product_price = 'P' . Carbon::now()->format('YmdHisu');
            $data['uid'] = $uid_product_price;
        }

        $process = DB::table('product_price')->updateOrInsert(
            ['uid' => $uid],
            $data
        );

        if ($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function edit_product_price(Request $request)
    {
        $uid = $request->uid;
        $data = $this->product->editProductPrice($uid);
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_product_price(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('product_price')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        if ($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function get_grosir_price(Request $request)
    {
        $product = $request->uid;
        $qty = $request->get('qty');
        $data = DB::table('product_price')->where('uid_product', $product)->where('first_quantity', '<=', $qty)->where('last_quantity', '>=', $qty)->where('status', 1)->first();
        if (!empty($data)) {
            return $this->ajaxResponse(true, 'Success!', $data);
        } else {
            return $this->ajaxResponse(false, 'No Wholesale Price');
        }
    }

    public function product_manual_Stock()
    {
        $title = 'Master Produk Khusus';
        $js = 'js/apps/master/product-manual-stock.js?_=' . rand();
        return view('master.product_manual_stock', compact('js', 'title'));
    }

    public function datatable_product_manual_stock(Request $request)
    {
        $data = $this->product->dataTableProductManualStock();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                if (Gate::allows('crudAccess', 'MD3', $row)) {
                    if ($row->status == 1) {
                        $btn = '<a class="btn btn-dim btn-outline-secondary btn-sm" onclick="edit(\'' . $row->uid . '\')"><em class="icon ni ni-edit"></em><span>Edit</span></a>
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="addprice(\'' . $row->uid . '\')"><em class="icon ni ni-coin-alt"></em><span>Harga Grosir</span></a>
                            ';
                    }
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store_product_manual_stock(Request $request)
    {
        $uid = $request->input('uid');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'product_categories' => 'required',
            'unit' => 'required',
            'cost_price' => 'required',
            'sell_price' => 'required',
            'retail_member_price' => 'required',
            'description' => 'required',
            'stock' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->ajaxResponse(false, $validator->errors()->first());
        }

        $user = Auth::user();

        $cost_price = Str::replace('.', '', $request->cost_price);
        $sell_price = Str::replace('.', '', $request->sell_price);
        $retail_member_price = Str::replace('.', '', $request->retail_member_price);

        $data = [
            'name' => $request->name,
            'uid_product_categories' => $request->product_categories,
            'uid_unit' => $request->unit,
            'cost_price' => $cost_price,
            'sell_price' => $sell_price,
            'retail_member_price' => $retail_member_price,
            'description' => $request->description,
            'stock' => $request->stock,
            'flag' => 2,
            'uid_company' => $user->uid_company
        ];

        if (!empty($uid)) {
            $data['update_at'] = Carbon::now();
            $data['update_by'] = $user->id;
        } else {
            $data['insert_at'] = Carbon::now();
            $data['insert_by'] = $user->id;
            $uid_product = 'P' . Carbon::now()->format('YmdHisu');
            $data['uid'] = $uid_product;
        }

        $process = DB::table('product')->updateOrInsert(
            ['uid' => $uid],
            $data
        );

        if ($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function get_retail_price(Request $request)
    {
        $uid_product = $request->uid;
        $data = Product::where('uid', $uid_product)->first();
        return $this->ajaxResponse(true, 'Success!', $data);
    }
}
