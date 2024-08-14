<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Models\ProductDamage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class ProductDamageController extends BaseController
{
    function __construct()
    {
        $this->product_damage = new ProductDamage();
    }

    public function index()
    {
        $title = 'Produk Rusak / Cacat';
        $js = 'js/apps/transactions/product-damage.js?_=' . rand();
        return view('transactions.product_damage.index', compact('js', 'title'));
    }

    public function datatable_product_damage(Request $request)
    {
        $user = Auth::user();
        $data = $this->product_damage->dataTableProductDamage();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) use ($user) {
                $btn = '';
                if (Gate::allows('crudAccess', 'TX5', $row)) {
                    if ($row->status == 1 && in_array($user->id_role, [1, 2])) {
                        $btn = '<a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                            ';
                    }
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function store_product_damage(Request $request)
    {
        $uid = $request->input('uid');

        $validator = Validator::make($request->all(), [
            'product' => 'required',
            'qty' => 'required'
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->ajaxResponse(false, $validator->errors()->first());
        }

        $uid_product = $request->product;
        $qty = Str::replace('.', '', $request->qty);

        // cek stock produk
        $check_stock = DB::table('product')->where('uid', $uid_product)->first();
        if($check_stock->stock < $qty) {
            return $this->ajaxResponse(false, 'Out of stock!');
        }

        $user = Auth::user();

        $data = [
            'uid_product' => $uid_product,
            'stock' => $qty,
            'note' => $request->note
        ];

        if (!empty($uid)) {
            $data['update_at'] = Carbon::now();
            $data['update_by'] = $user->id;
        } else {
            $data['insert_at'] = Carbon::now();
            $data['insert_by'] = $user->id;
            $uid_product_damage = 'PD' . Carbon::now()->format('YmdHisu');
            $data['uid'] = $uid_product_damage;
        }

        $process = DB::table('product_damage')->updateOrInsert(
            ['uid' => $uid],
            $data
        );

        if ($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function edit_product_damage(Request $request)
    {
        $uid = $request->uid;
        $data = $this->product_damage->editProductDamage($uid);
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_product_damage(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('product_damage')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        if ($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }
}
