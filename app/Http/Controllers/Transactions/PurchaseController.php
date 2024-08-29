<?php

namespace App\Http\Controllers\Transactions;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseOrderExport;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDF;

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
        $min = !empty($request->min) ? date('Y-m-d', strtotime($request->min)) . ' 00:00:00' : '';
        $max = !empty($request->max) ? date('Y-m-d', strtotime($request->max)) . ' 23:59:59' : '';
        $role = Auth::user()->id_role;

        $data = $this->purchase_order->dataTablePurchaseOrders($min, $max, $role);
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $role = Auth::user()->id_role;
                if (Gate::allows('crudAccess', 'TX1', $row)) {
                    if ($role == 1) {
                        $btn = '<a href="/transaction/purchase/add?uid=' . $row->uid . '" class="btn btn-dim btn-outline-secondary btn-sm"><em class="icon ni ni-edit"></em><span>Edit</span></a>&nbsp;
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>&nbsp';
                    }
                    // $btn .= '<a class="btn btn-dim btn-outline-secondary btn-sm" href="/transaction/purchase/add?uid=' . $row->uid . '"><em class="icon ni ni-eye"></em><span>View</span></a>&nbsp';
                    $btn .= '<a class="btn btn-dim btn-outline-secondary btn-sm" target="_blank" href="/transaction/purchase/po/' . $row->uid . '"><em class="icon ni ni-send"></em><span>Purchase Order</span></a>';
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
        $uid_company = $user->uid_company;
        if (!empty($uid)) {
            $no_po = $request->po_number;
            $get_existing_header = DB::table('purchase_orders')->where('uid', $uid)->first();
            $uid_company = $get_existing_header->uid_company;
            $get_existing_detail = DB::table('purchase_order_details')->where('po_number', $no_po)->where('uid_company', $uid_company)->get();
            foreach ($get_existing_detail as $old) {
                //update stock back
                try {
                    $update_stock = DB::table('product')->where('uid', $old->uid_product)->where('uid_company', $uid_company)->decrement('stock', $old->qty);
                } catch (\Throwable $e) {
                    DB::rollBack();
                    return $this->ajaxResponse(false, 'Failed to update stock', $e);
                }
            }

            try {
                //delete detail
                $del_detail = DB::table('purchase_order_details')->where('po_number', $no_po)->where('uid_company', $uid_company)->delete();
            } catch (\Throwable $e) {
                DB::rollBack();
                return $this->ajaxResponse(false, 'Failed to save data', $e);
            }

        } else {
            $no_po = "PO" . date('mdY');
            $get_last_number = DB::table("purchase_orders")->where("po_number", "like", "$no_po%")->where("uid_company", $uid_company)->orderBy('po_number', 'desc')->count();
            $no_po .= '-' . ++$get_last_number;

            $loop = true;
            while ($loop) {
                $validate_po_number = DB::table('purchase_orders')->where("po_number", $no_po)->where('uid_company', $uid_company)->count();
                if ($validate_po_number == 0) {
                    $loop = false;
                } else {
                    $no_po = "PO" . date('mdY') . '-' . ++$get_last_number;
                }
            }

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
                    'insert_by' => $user->id,
                    'status' => 1,
                    'uid_company' => $uid_company
                ]);
            } catch (\Throwable $e) {
                DB::rollBack();
                return $this->ajaxResponse(false, 'Failed to save data', $e);
            }

            try {
                //update stock
                $update_stock = DB::table('product')->where('uid', $request->details['products'][$i])->where('uid_company', $uid_company)->increment('stock', $request->details['qty'][$i]);
            } catch (\Throwable $e) {
                DB::rollBack();
                return $this->ajaxResponse(false, 'Failed to update stock', $e);
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
                $data['update_by'] = $user->id;

            } else {
                $data['insert_at'] = Carbon::now();
                $data['insert_by'] = $user->id;
                $uid_purchase_order = 'PO' . Carbon::now()->format('YmdHisu');
                $data['uid'] = $uid_purchase_order;
                $data['uid_company'] = $user->uid_company;
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
        $data['header'] = db::table('purchase_orders as po')->join('supplier as sup', 'sup.uid', 'po.uid_supplier')->select('po.uid', 'po.po_number', 'po.uid_supplier', 'po.transaction_date', 'sup.name', 'po.discount', 'po.uid_company')->where('po.uid', $uid)->where('po.status', 1)->first();
        $data['detail'] = db::table('purchase_order_details as pd')->join('product as p', 'p.uid', 'pd.uid_product')->join('unit as u', 'u.uid', 'pd.uid_unit')->select('pd.po_number', 'pd.uid_product', 'p.name as product_name', 'pd.uid_unit', 'u.name as unit_name', 'pd.qty', 'pd.price')->where('pd.po_number', $data['header']->po_number)->where('pd.uid_company', $data['header']->uid_company)->where('pd.status', 1)->get()->toArray();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_purchase_order(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $get_po_number = DB::table('purchase_orders')->select('po_number', 'uid_company')->where('uid', $uid)->first();
        $po_number = $get_po_number->po_number;
        $uid_company = $get_po_number->uid_company;
        //check future stock
        $detail_po = DB::table('purchase_order_details')->where('po_number', $po_number)->where('uid_company', $uid_company)->get();
        foreach ($detail_po as $po) {
            $get_existing_stock = DB::table('product')->where('uid', $po->uid_product)->where('uid_company', $uid_company)->first();
            $existing_stock = $get_existing_stock->stock;
            $qty = $po->qty;
            $future_stock = $existing_stock - $qty;

            if ($future_stock < 0) {
                return $this->ajaxResponse(false, 'Failed to delete, Out of Stock');
            }
        }


        $process = DB::table('purchase_orders')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        $del_detail = DB::table('purchase_order_details')->where('po_number', $po_number)->where('uid_company', $uid_company)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        $get_existing_detail = DB::table('purchase_order_details')->where('po_number', $po_number)->where('uid_company', $uid_company)->get();
        foreach ($get_existing_detail as $old) {
            //update stock back
            try {
                $update_stock = DB::table('product')->where('uid', $old->uid_product)->where('uid_company', $uid_company)->decrement('stock', $old->qty);
            } catch (\Throwable $e) {
                DB::rollBack();
                return $this->ajaxResponse(false, 'Failed to update stock', $e);
            }
        }


        if ($process && $del_detail) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function export_excel(Request $request)
    {
        $min = !empty($request->min) ? date('Y-m-d', strtotime($request->min)) . ' 00:00:00' : '';
        $max = !empty($request->max) ? date('Y-m-d', strtotime($request->max)) . ' 23:59:59' : '';
        return Excel::download(new PurchaseOrderExport($min, $max), 'Pembelian.xlsx');
    }

    public function print_pdf(Request $request)
    {
        $uid = $request->uid;
        $data['header'] = db::table('purchase_orders as po')->join('supplier as sup', 'sup.uid', 'po.uid_supplier')->select('po.uid', 'po.po_number', 'po.uid_supplier', 'po.transaction_date', 'sup.name', 'sup.phone', 'po.discount', 'po.tax_rate', 'po.tax_value', 'po.grand_total', 'po.uid_company')->where('po.uid', $uid)->where('po.status', 1)->first();
        $data['detail'] = db::table('purchase_order_details as pd')->join('product as p', 'p.uid', 'pd.uid_product')->join('unit as u', 'u.uid', 'pd.uid_unit')->select('pd.po_number', 'pd.uid_product', 'p.name as product_name', 'pd.uid_unit', 'u.name as unit_name', 'pd.qty', 'pd.price', 'pd.note')->where('pd.po_number', $data['header']->po_number)->where('pd.status', 1)->where('pd.uid_company', $data['header']->uid_company)->get()->toArray();
        $data['company'] = DB::table('company')->where('uid', $data['header']->uid_company)->first();


        // dd($data);
        $pdf = PDF::loadview('transactions.purchase_order.po', ['data' => $data])->setPaper('A5', 'landscape');
        return $pdf->stream('PO-' . $data['header']->po_number);
        // return view('transactions.sales_order.invoice', ['data' => $data]);
    }



    public function check_stock(Request $request)
    {
        $response_status = true;
        $response_message = "Ready Stock";
        $user = Auth::user();
        $uid_company = $request->uid_company;
        $data = array();
        if (!empty($uid)) {

            $no_po = $request->po_number;
            for ($i = 0; $i < sizeof($request->details['products']); $i++) {

                $uid_product = $request->details['products'][$i];
                $get_stock = DB::table('product')->where('uid', $uid_product)->where('uid_company', $uid_company)->first();
                $get_existing = DB::table('purchase_order_details')->where('uid_product', $uid_product)->where('po_number', $no_po)->where('uid_company', $uid_company)->first();

                $qty = $request->details['qty'][$i];
                $stock = $get_stock->stock;
                $existing = $get_existing->qty;

                $future_stock = $stock - $existing + $qty;

                if ($future_stock < 0) {
                    $low_stock = array();
                    $low_stock['product'] = $uid_product;
                    $low_stock['stock'] = $future_stock;
                    $data[] = $low_stock;
                    $response_status = false;
                    $response_message = "Out of Stock";
                }

            }

        }
        return $this->ajaxResponse($response_status, $response_message, $data);

    }


    public function origin_number($number = 0)
    {
        $number = str_replace('.', '', $number);
        $number = str_replace(',', '.', $number);
        return $number;
    }

    public function datatable_detail_purchase_order(Request $request)
    {
        $uid = $request->uid;
        $data = $this->purchase_order->dataTableDetailPurchaseOrder($uid);
        return Datatables::of($data)->addIndexColumn()->make(true);
    }

}
