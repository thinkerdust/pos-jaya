<?php

namespace App\Http\Controllers\Transactions;

use App\Exports\PendingTransactionExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesOrderExport;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDF;

class SalesController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->sales_order = new SalesOrder();
    }

    public function index()
    {
        $title = 'Penjualan';
        $js = 'js/apps/transactions/sales_order.js?_=' . rand();
        return view('transactions.sales_order.index', compact('title', 'js'));
    }

    public function pending()
    {
        $title = 'Pending Transaksi';
        $js = 'js/apps/transactions/pending.js?_=' . rand();
        return view('transactions.sales_order.pending', compact('title', 'js'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function datatable_sales_order(Request $request)
    {
        $data = $this->sales_order->dataTableSalesOrders();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                if (Gate::allows('crudAccess', 'TX2', $row)) {
                    $btn = '<a href="/transaction/sales/add?uid=' . $row->uid . '" class="btn btn-dim btn-outline-secondary btn-sm"><em class="icon ni ni-edit"></em><span>Edit</span></a>&nbsp;
                    <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                    <a class="btn btn-dim btn-outline-secondary btn-sm" target="_blank" href="/transaction/sales/invoice/' . $row->uid . '"><em class="icon ni ni-send"></em><span>Invoice</span></a>';

                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function datatable_pending(Request $request)
    {
        $data = $this->sales_order->dataTablePending();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                if (Gate::allows('crudAccess', 'TX2', $row)) {
                    $btn = '<a href="/transaction/sales/add?uid=' . $row->uid . '" class="btn btn-dim btn-outline-secondary btn-sm"><em class="icon ni ni-edit"></em><span>Edit</span></a>&nbsp;
                                <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                    <a class="btn btn-dim btn-outline-secondary btn-sm" target="_blank" href="/transaction/sales/invoice/' . $row->uid . '"><em class="icon ni ni-send"></em><span>Invoice</span></a>';


                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function add_sales_order(Request $request)
    {
        $uid = $request->uid;
        $js = 'js/apps/transactions/sales_order.js?_=' . rand();
        return view('transactions.sales_order.add_sales_order', compact('js', 'uid'));
    }

    public function store_sales_order(Request $request)
    {
        // dd($request);
        $uid = $request->input('uid_sales_order');

        $validator = Validator::make($request->all(), [
            'customer' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->ajaxResponse(false, $validator->errors()->first());
        }


        $user = Auth::user();

        if (!empty($uid)) {
            $no_inv = $request->invoice_number;
            try {
                //delete detail
                $del_detail = DB::table('sales_order_details')->where('invoice_number', $no_inv)->delete();
            } catch (\Throwable $e) {
                DB::rollBack();
                return $this->ajaxResponse(false, 'Failed to save data', $e);
            }

        } else {
            $no_inv = "INV" . date('mdY');
            $get_last_number = DB::table("sales_orders")->where("invoice_number", "like", "$no_inv%")->orderBy('invoice_number', 'desc')->count();
            $no_inv .= '-' . ++$get_last_number;

        }

        //insert detail
        $subtotal = 0;
        $grand_total = 0;
        $disc = 0;
        for ($i = 0; $i < sizeof($request->details['products']); $i++) {
            try {
                $insert_detail = DB::table('sales_order_details')->updateOrInsert([
                    'uid' => 'SD' . Carbon::now()->format('YmdHisu'),
                    'invoice_number' => $no_inv,
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
            $tax_rate = isset($request->ppn) ? $request->ppn : 0;
            $tax_value = isset($request->ppn_value) ? $this->origin_number($request->ppn_value) : 0;

            // dd($tax_value);
            $grand_total = $subtotal - $disc + $tax_value;
            $data = [
                'invoice_number' => $no_inv,
                'uid_customer' => $request->customer,
                'transaction_date' => Carbon::now(),
                'collection_date' => $request->collection_date,
                'priority' => $request->priority,
                'note' => '',
                'subtotal' => $subtotal,
                'discount' => $disc,
                'disc_rate' => $request->disc_global,
                'tax_rate' => $tax_rate,
                'tax_value' => $tax_value,
                'grand_total' => $grand_total,
                'pending' => $request->pending,
                'status' => 1
            ];

            if (!empty($uid)) {
                $data['update_at'] = Carbon::now();
                $data['update_by'] = $user->username;
            } else {
                $data['insert_at'] = Carbon::now();
                $data['insert_by'] = $user->username;
                $uid_purchase_order = 'SO' . Carbon::now()->format('YmdHisu');
                $data['uid'] = $uid_purchase_order;
            }


            DB::table('sales_orders')->updateOrInsert(
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

    public function edit_sales_order(Request $request)
    {
        $uid = $request->uid;
        $data['header'] = db::table('sales_orders as so')->join('customer as cus', 'cus.uid', 'so.uid_customer')->select('so.uid', 'so.invoice_number', 'so.uid_customer', 'so.transaction_date', 'cus.name', 'so.discount', 'so.disc_rate', 'so.tax_rate', 'so.tax_value', 'so.grand_total', 'so.collection_date', 'so.priority')->where('so.uid', $uid)->first();
        $data['detail'] = db::table('sales_order_details as pd')->join('product as p', 'p.uid', 'pd.uid_product')->join('unit as u', 'u.uid', 'pd.uid_unit')->select('pd.invoice_number', 'pd.uid_product', 'p.name as product_name', 'pd.uid_unit', 'u.name as unit_name', 'pd.qty', 'pd.price')->where('pd.invoice_number', $data['header']->invoice_number)->get()->toArray();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_sales_order(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('sales_orders')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->username]);

        $get_invoice_number = DB::table('sales_orders')->select('invoice_number')->where('uid', $uid)->first();
        $invoice_number = $get_invoice_number->invoice_number;
        $del_detail = DB::table('sales_order_details')->where('invoice_number', $invoice_number)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->username]);

        if ($process && $del_detail) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }


    public function print_pdf(Request $request)
    {
        $uid = $request->uid;
        $data['header'] = db::table('sales_orders as so')->join('customer as cus', 'cus.uid', 'so.uid_customer')->select('so.uid', 'so.invoice_number', 'so.uid_customer', 'so.transaction_date', 'cus.name', 'cus.phone', 'so.discount', 'so.disc_rate', 'so.tax_rate', 'so.tax_value', 'so.grand_total', 'so.collection_date', 'so.priority')->where('so.uid', $uid)->first();
        $data['detail'] = db::table('sales_order_details as pd')->join('product as p', 'p.uid', 'pd.uid_product')->join('unit as u', 'u.uid', 'pd.uid_unit')->select('pd.invoice_number', 'pd.uid_product', 'p.name as product_name', 'pd.uid_unit', 'u.name as unit_name', 'pd.qty', 'pd.price')->where('pd.invoice_number', $data['header']->invoice_number)->get()->toArray();

        $pdf = PDF::loadview('transactions.sales_order.invoice', ['data' => $data])->setPaper('A5', 'landscape');
        return $pdf->stream('Invoice-' . $data['header']->invoice_number);
        // return view('transactions.sales_order.invoice', ['data' => $data]);
    }

    public function export_excel()
    {
        return Excel::download(new SalesOrderExport, 'Penjualan.xlsx');
    }

    public function export_excel_pending()
    {
        return Excel::download(new PendingTransactionExport, 'Pending.xlsx');
    }


    public function origin_number($number = 0)
    {
        $number = str_replace('.', '', $number);
        $number = str_replace(',', '.', $number);
        return $number;
    }
}
