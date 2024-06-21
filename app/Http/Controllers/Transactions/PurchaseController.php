<?php

namespace App\Http\Controllers\Transactions;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseOrderExport;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\PurchaseOrder;
use App\Models\Material;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->purchase_order = new PurchaseOrder();
    }

    public function index()
    {
        $title = 'Pembelian';
        $js = 'js/apps/transactions/purchase_order.js?_=' . rand();
        return view('transactions.purchase_order.index', compact('title', 'js'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function datatable_purchase_order(Request $request)
    {
        $data = $this->purchase_order->dataTablePurchaseOrders();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                if (Gate::allows('crudAccess', 'TX1', $row)) {
                    $btn = '<a href="/transaction/purchase/add?uid=' . $row->uid . '" class="btn btn-dim btn-outline-secondary btn-sm"><em class="icon ni ni-edit"></em><span>Edit</span></a>&nbsp;
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>';

                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function add_purchase_order(Request $request)
    {
        $uid = $request->uid;
        $js = 'js/apps/transactions/purchase_order.js?_=' . rand();
        return view('transactions.purchase_order.add_purchase_order', compact('js', 'uid'));
    }

    public function store_purchase_order(Request $request)
    {
        $uid = $request->input('uid_purchase_order');

        $validator = Validator::make($request->all(), [
            'supplier' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->ajaxResponse(false, $validator->errors()->first());
        }


        $user = Auth::user();

        if (!empty($uid)) {
            $no_po = $request->po_number;
            try {
                //delete detail
                $del_detail = DB::table('purchase_order_details')->where('po_number', $no_po)->delete();
            } catch (\Throwable $e) {
                DB::rollBack();
                return $this->ajaxResponse(false, 'Failed to save data', $e);
            }

        } else {
            $no_po = "PO" . date('mdY');
            $get_last_number = DB::table("purchase_orders")->where("po_number", "like", "$no_po%")->orderBy('po_number', 'desc')->count();
            $no_po .= '-' . ++$get_last_number;

        }

        //insert detail
        $subtotal = 0;
        $grand_total = 0;
        $disc = 0;
        for ($i = 0; $i < sizeof($request->details['products']); $i++) {
            try {
                $insert_detail = DB::table('purchase_order_details')->updateOrInsert([
                    'uid' => 'PD' . Carbon::now()->format('YmdHisu'),
                    'po_number' => $no_po,
                    'uid_product' => $request->details['products'][$i],
                    'qty' => $request->details['qty'][$i],
                    'uid_unit' => $request->details['units'][$i],
                    'price' => $request->details['prices'][$i],
                    'discount' => 0,
                    'note' => '',
                    'insert_at' => Carbon::now(),
                    'insert_by' => $user->username
                ]);
            } catch (\Throwable $e) {
                DB::rollBack();
                return $this->ajaxResponse(false, 'Failed to save data', $e);
            }

            $subtotal += ($request->details['qty'][$i] * $request->details['prices'][$i]);
        }



        DB::beginTransaction();
        try {

            $disc = isset($request->disc) ? $this->origin_number($request->disc) : 0;
            $grand_total = $subtotal - $disc;
            $data = [
                'po_number' => $no_po,
                'uid_supplier' => $request->supplier,
                'transaction_date' => Carbon::now(),
                'note' => '',
                'subtotal' => $subtotal,
                'discount' => $disc,
                'tax_rate' => 0,
                'tax_value' => 0,
                'grand_total' => $grand_total,
                'status' => 1
            ];

            if (!empty($uid)) {
                $data['update_at'] = Carbon::now();
                $data['update_by'] = $user->username;
            } else {
                $data['insert_at'] = Carbon::now();
                $data['insert_by'] = $user->username;
                $uid_purchase_order = 'PO' . Carbon::now()->format('YmdHisu');
                $data['uid'] = $uid_purchase_order;
            }


            DB::table('purchase_orders')->updateOrInsert(
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

    public function edit_purchase_order(Request $request)
    {
        $uid = $request->uid;
        $data['header'] = db::table('purchase_orders as po')->join('supplier as sup', 'sup.uid', 'po.uid_supplier')->select('po.uid', 'po.po_number', 'po.uid_supplier', 'po.transaction_date', 'sup.name', 'po.discount')->where('po.uid', $uid)->first();
        $data['detail'] = db::table('purchase_order_details as pd')->join('product as p', 'p.uid', 'pd.uid_product')->join('unit as u', 'u.uid', 'pd.uid_unit')->select('pd.po_number', 'pd.uid_product', 'p.name as product_name', 'pd.uid_unit', 'u.name as unit_name', 'pd.qty', 'pd.price')->where('pd.po_number', $data['header']->po_number)->get()->toArray();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_purchase_order(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('purchase_orders')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->username]);

        $get_po_number = DB::table('purchase_orders')->select('po_number')->where('uid', $uid)->first();
        $po_number = $get_po_number->po_number;
        $del_detail = DB::table('purchase_order_details')->where('po_number', $po_number)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->username]);

        if ($process && $del_detail) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function export_excel()
    {
        return Excel::download(new PurchaseOrderExport, 'Pembelian.xlsx');
    }



    public function origin_number($number = 0)
    {
        $number = str_replace('.', '', $number);
        $number = str_replace(',', '.', $number);
        return $number;
    }

}
