<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Exports\PendingTransactionExport;
use App\Exports\SalesOrderExport;
use App\Exports\ReportTransactionExport;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
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
        $last_month = Carbon::now()->subMonths(3);
        $last_month = $last_month->format('Y-m-d');
        $min = !empty($request->min) ? date('Y-m-d', strtotime($request->min)) . ' 00:00:00' : date('Y-m-01', strtotime($last_month)) . ' 00:00:00';
        $max = !empty($request->max) ? date('Y-m-d', strtotime($request->max)) . ' 23:59:59' : date('Y-m-t 23:59:59');
        $status = $request->status;
        $role = Auth::user()->id_role;

        $data = $this->sales_order->dataTableSalesOrders($min, $max, $status, $role);
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $role = Auth::user()->id_role;
                $cek_pembayaran = DB::table('receivable_payments')->where('invoice_number', $row->invoice_number)->where('uid_company', $row->uid_company, )->where('status', 1)->count();
                if (Gate::allows('crudAccess', 'TX2', $row)) {
                    if ($cek_pembayaran == 0) {
                        if ($role == 1) {
                            $btn = '<a href="/transaction/sales/add?uid=' . $row->uid . '" class="btn btn-dim btn-outline-secondary btn-sm"><em class="icon ni ni-edit"></em><span>Edit</span></a>
                            <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>';
                        }
                    }


                    $btn .= '<a class="btn btn-dim btn-outline-secondary btn-sm" target="_blank" href="/transaction/sales/invoice/' . $row->uid . '"><em class="icon ni ni-send"></em><span>Invoice</span></a>
                    <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="bayar(\'' . $row->uid . '\')"><em class="icon ni ni-money"></em><span>Pembayaran</span></a>';
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function datatable_pending(Request $request)
    {
        $last_month = Carbon::now()->subMonths(3);
        $last_month = $last_month->format('Y-m-d');
        $min = !empty($request->min) ? date('Y-m-d', strtotime($request->min)) . ' 00:00:00' : date('Y-m-01', strtotime($last_month)) . ' 00:00:00';
        $max = !empty($request->max) ? date('Y-m-d', strtotime($request->max)) . ' 23:59:59' : date('Y-m-t 23:59:59');
        $role = Auth::user()->id_role;

        $data = $this->sales_order->dataTablePending($min, $max, $role);
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
        $pending = DB::table('sales_orders as so')->where('uid', $uid)->select('pending')->first();
        $js = 'js/apps/transactions/sales_order.js?_=' . rand();
        return view('transactions.sales_order.add_sales_order', compact('js', 'uid', 'pending'));
    }

    public function store_sales_order(Request $request)
    {
        $uid = $request->input('uid_sales_order');

        $validator = Validator::make($request->all(), [
            'customer' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->ajaxResponse(false, $validator->errors()->first());
        }


        $user = Auth::user();
        $uid_company = $user->uid_company;
        if (!empty($uid)) {
            $no_inv = $request->invoice_number;
            $get_existing_header = DB::table('sales_orders')->where('uid', $uid)->first();
            $uid_company = $get_existing_header->uid_company;
            $get_existing_detail = DB::table('sales_order_details')->where('invoice_number', $no_inv)->where('uid_company', $uid_company)->get();

            try {
                //delete detail
                $del_detail = DB::table('sales_order_details')->where('invoice_number', $no_inv)->where('uid_company', $uid_company)->delete();
            } catch (\Throwable $e) {
                DB::rollBack();
                return $this->ajaxResponse(false, 'Failed to save data', $e);
            }

            if ($get_existing_header->pending == 0) {
                foreach ($get_existing_detail as $old) {
                    if ($request->pending == 0) {
                        //update stock back
                        try {
                            $update_stock = DB::table('product')->where('uid', $old->uid_product)->where('uid_company', $uid_company)->increment('stock', $old->qty);
                        } catch (\Throwable $e) {
                            DB::rollBack();
                            return $this->ajaxResponse(false, 'Failed to update stock', $e);
                        }
                    }
                }
            } else {
                //create new invoice number
                if ($request->pending == 0) {
                    $no_inv = "INV" . date('mdY');
                    $get_last_number = DB::table("sales_orders")->where("invoice_number", "like", "$no_inv%")->where('uid_company', $uid_company)->orderBy('invoice_number', 'desc')->count();
                    $no_inv .= '-' . ++$get_last_number;

                    $loop = true;
                    while ($loop) {
                        $validate_inv_number = DB::table('sales_orders')->where("invoice_number", $no_inv)->where('uid_company', $uid_company)->count();
                        if ($validate_inv_number == 0) {
                            $loop = false;
                        } else {
                            $no_inv = "INV" . date('mdY') . '-' . ++$get_last_number;
                        }
                    }
                }

            }


        } else {

            if ($request->pending == 0) {
                $no_inv = "INV" . date('mdY');
                $get_last_number = DB::table("sales_orders")->where("invoice_number", "like", "$no_inv%")->where('uid_company', $uid_company)->orderBy('invoice_number', 'desc')->count();
                $no_inv .= '-' . ++$get_last_number;

                $loop = true;
                while ($loop) {
                    $validate_inv_number = DB::table('sales_orders')->where("invoice_number", $no_inv)->where('uid_company', $uid_company)->count();
                    if ($validate_inv_number == 0) {
                        $loop = false;
                    } else {
                        $no_inv = "INV" . date('mdY') . '-' . ++$get_last_number;
                    }
                }
            } else {
                $no_inv = "TMP" . date('mdY');
                $get_last_number = DB::table("sales_orders")->where("invoice_number", "like", "$no_inv%")->where('uid_company', $uid_company)->orderBy('invoice_number', 'desc')->count();
                $no_inv .= '-' . ++$get_last_number;

                $loop = true;
                while ($loop) {
                    $validate_inv_number = DB::table('sales_orders')->where("invoice_number", $no_inv)->where('uid_company', $uid_company)->count();
                    if ($validate_inv_number == 0) {
                        $loop = false;
                    } else {
                        $no_inv = "TMP" . date('mdY') . '-' . ++$get_last_number;
                    }
                }

            }

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
                    'length' => $request->details['length'][$i],
                    'width' => $request->details['width'][$i],
                    'discount' => 0,
                    'note' => $request->details['notes'][$i],
                    'insert_at' => Carbon::now(),
                    'insert_by' => $user->id,
                    'uid_company' => $uid_company

                ]);
            } catch (\Throwable $e) {
                DB::rollBack();
                return $this->ajaxResponse(false, 'Failed to save data detail', $e);
            }

            $subtotal += ($request->details['subtotal'][$i]);

            if ($request->pending == 0) {
                try {
                    //update stock
                    $update_stock = DB::table('product')->where('uid', $request->details['products'][$i])->where('uid_company', $user->uid_company)->decrement('stock', $request->details['qty'][$i]);
                } catch (\Throwable $e) {
                    DB::rollBack();
                    return $this->ajaxResponse(false, 'Failed to update stock', $e);
                }

            }
        }



        DB::beginTransaction();
        try {

            $disc = isset($request->disc) ? $this->origin_number($request->disc) : 0;
            $tax_rate = isset($request->ppn) ? $request->ppn : 0;
            $tax_value = isset($request->ppn_value) ? $this->origin_number($request->ppn_value) : 0;
            $proofing = $this->origin_number($request->proofing);
            $grand_total = $subtotal - $disc + $tax_value + $proofing;
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
                'status' => 1,
                'proofing' => $proofing,
                'note' => $request->keterangan,
            ];

            if (!empty($uid)) {
                $data['update_at'] = Carbon::now();
                $data['update_by'] = $user->id;
            } else {
                $data['insert_at'] = Carbon::now();
                $data['insert_by'] = $user->id;
                $uid_sales_order = 'SO' . Carbon::now()->format('YmdHisu');
                $data['uid'] = $uid_sales_order;
                $data['uid_company'] = $user->uid_company;
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
        $data['header'] = db::table('sales_orders as so')->join('customer as cus', 'cus.uid', 'so.uid_customer')->select('so.uid', 'so.invoice_number', 'so.uid_customer', 'so.transaction_date', 'cus.name', 'so.discount', 'so.disc_rate', 'so.tax_rate', 'so.tax_value', 'so.grand_total', 'so.collection_date', 'so.priority', 'so.proofing', 'so.note', 'so.uid_company')->where('so.uid', $uid)->where('so.status', 1)->first();
        $data['detail'] = db::table('sales_order_details as pd')->join('product as p', 'p.uid', 'pd.uid_product')->join('unit as u', 'u.uid', 'pd.uid_unit')->select('pd.invoice_number', 'pd.uid_product', 'p.name as product_name', 'pd.uid_unit', 'u.name as unit_name', 'pd.qty', 'pd.price', 'p.stock', 'pd.note', 'pd.length', 'pd.width')->where('pd.invoice_number', $data['header']->invoice_number)->where('pd.status', 1)->where('pd.uid_company', $data['header']->uid_company)->get()->toArray();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_sales_order(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('sales_orders')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        $get_invoice_number = DB::table('sales_orders')->select('invoice_number', 'pending', 'uid_company')->where('uid', $uid)->first();
        $invoice_number = $get_invoice_number->invoice_number;
        $pending = $get_invoice_number->pending;
        $uid_company = $get_invoice_number->uid_company;
        $del_detail = DB::table('sales_order_details')->where('invoice_number', $invoice_number)->where('uid_company', $uid_company)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        //update stock
        $get_existing_detail = DB::table('sales_order_details')->where('invoice_number', $invoice_number)->where('uid_company', $uid_company)->get();
        foreach ($get_existing_detail as $old) {
            if ($pending == 0) {
                //update stock back
                try {
                    $update_stock = DB::table('product')->where('uid', $old->uid_product)->where('uid_company', $uid_company)->increment('stock', $old->qty);
                } catch (\Throwable $e) {
                    DB::rollBack();
                    return $this->ajaxResponse(false, 'Failed to update stock', $e);
                }
            }
        }


        if ($process && $del_detail) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }

    public function check_stock(Request $request)
    {
        $response_status = true;
        $response_message = "Ready Stock";
        $user = Auth::user();
        $uid_company = $request->uid_company;
        $data = array();
        for ($i = 0; $i < sizeof($request->details['products']); $i++) {

            $uid_product = $request->details['products'][$i];
            $qty = $request->details['qty'][$i];
            $get_stock = DB::table('product')->where('uid', $uid_product)->where('uid_company', $uid_company)->first();
            if (!empty($get_stock)) {
                if ($get_stock->stock < $qty) {
                    $low_stock = array();
                    $low_stock['product'] = $uid_product;
                    $low_stock['stock'] = $get_stock->stock;
                    $data[] = $low_stock;
                    $response_status = false;
                    $response_message = "Out of Stock";
                }
            } else {
                $response_status = false;
                $response_message = "Product not Found or has different company";
                $data[] = $uid_product;
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

    public function print_pdf(Request $request)
    {
        $uid = $request->uid;
        $data['header'] = db::table('sales_orders as so')->join('customer as cus', 'cus.uid', 'so.uid_customer')->select('so.uid', 'so.invoice_number', 'so.uid_customer', 'so.transaction_date', 'cus.name', 'cus.phone', 'so.discount', 'so.disc_rate', 'so.tax_rate', 'so.tax_value', 'so.grand_total', 'so.collection_date', 'so.priority', 'so.uid_company', 'so.proofing', 'so.paid_off')->where('so.uid', $uid)->where('so.status', 1)->first();
        $data['detail'] = db::table('sales_order_details as pd')->join('product as p', 'p.uid', 'pd.uid_product')->join('unit as u', 'u.uid', 'pd.uid_unit')->select('pd.invoice_number', 'pd.uid_product', 'p.name as product_name', 'pd.uid_unit', 'u.name as unit_name', 'pd.qty', 'pd.price', 'pd.note', 'pd.length', 'pd.width')->where('pd.invoice_number', $data['header']->invoice_number)->where('pd.status', 1)->where('pd.uid_company', $data['header']->uid_company)->get()->toArray();
        $data['receipt'] = DB::table('receivable_payments as rp')->where('rp.invoice_number', $data['header']->invoice_number)->where('status', 1)->where('uid_company', $data['header']->uid_company)->sum('amount');
        $data['company'] = DB::table('company')->where('uid', $data['header']->uid_company)->first();

        $pdf = PDF::loadview('transactions.sales_order.invoice', ['data' => $data])->setPaper('A5', 'landscape');
        return $pdf->stream('Invoice-' . $data['header']->invoice_number);
    }

    public function export_excel(Request $request)
    {
        $last_month = Carbon::now()->subMonths(3);
        $last_month = $last_month->format('Y-m-d');
        $min = !empty($request->min) ? date('Y-m-d', strtotime($request->min)) . ' 00:00:00' : date('Y-m-01', strtotime($last_month)) . ' 00:00:00';
        $max = !empty($request->max) ? date('Y-m-d', strtotime($request->max)) . ' 23:59:59' : '';
        $status = $request->status;
        return Excel::download(new SalesOrderExport($min, $max, $status), 'Penjualan.xlsx');
    }

    public function export_excel_pending(Request $request)
    {
        $last_month = Carbon::now()->subMonths(3);
        $last_month = $last_month->format('Y-m-d');
        $min = !empty($request->min) ? date('Y-m-d', strtotime($request->min)) . ' 00:00:00' : date('Y-m-01', strtotime($last_month)) . ' 00:00:00';
        $max = !empty($request->max) ? date('Y-m-d', strtotime($request->max)) . ' 23:59:59' : '';
        return Excel::download(new PendingTransactionExport($min, $max), 'Pending.xlsx');
    }

    public function export_report_transaction(Request $request)
    {
        $last_month = Carbon::now()->subMonths(3);
        $last_month = $last_month->format('Y-m-d');
        $min = !empty($request->min) ? date('Y-m-d', strtotime($request->min)) . ' 00:00:00' : date('Y-m-01', strtotime($last_month)) . ' 00:00:00';
        $max = !empty($request->max) ? date('Y-m-d', strtotime($request->max)) . ' 23:59:59' : date('Y-m-t') . ' 23:59:59';
        return Excel::download(new ReportTransactionExport($min, $max), 'Reports_'.date('Ymd').'.xlsx');
    }
}
