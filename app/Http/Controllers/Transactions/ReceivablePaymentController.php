<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\ReceivablePayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use PDF;
use App\Exports\ReceivablePaymentExport;
use Maatwebsite\Excel\Facades\Excel;



class ReceivablePaymentController extends BaseController
{
    function __construct()
    {
        $this->receivable_payment = new ReceivablePayment();
    }

    public function index()
    {
        $title = 'Penerimaan';
        $js = 'js/apps/transactions/receivable_payment.js?_=' . rand();
        return view('transactions.receivable_payment.index', compact('title', 'js'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function datatable_receivable_payment(Request $request)
    {
        $min = !empty($request->min) ? date('Y-m-d', strtotime($request->min)) . ' 00:00:00' : '';
        $max = !empty($request->max) ? date('Y-m-d', strtotime($request->max)) . ' 23:59:00' : '';
        $payment_method = !empty($request->payment) ? $request->payment : '';
        $role = Auth::user()->id_role;

        $data = $this->receivable_payment->dataTableReceivablePayments($min, $max, $role, $payment_method);
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                if (Gate::allows('crudAccess', 'TX4', $row)) {
                    $btn = '<a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                    <a class="btn btn-dim btn-outline-secondary btn-sm" target="_blank" href="/transaction/receivable_payment/receipt/' . $row->uid . '"><em class="icon ni ni-send"></em><span>Nota</span></a>';
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }



    public function add_receivable_payment(Request $request)
    {
        $uid = $request->uid;
        $js = 'js/apps/transactions/receivable_payment.js?_=' . rand();
        return view('transactions.receivable_payment.add_receivable_payment', compact('js', 'uid'));
    }

    public function store_receivable_payment(Request $request)
    {
        $uid = $request->input('modal_uid');

        $validator = Validator::make($request->all(), [
            'modal_noinv' => 'required',
            'modal_amount' => 'required',
            'modal_payment_method' => 'required',
            'modal_selisih' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->ajaxResponse(false, $validator->errors()->first());
        }

        $no_inv = $request->modal_noinv;

        $amount = $this->origin_number($request->modal_amount);
        $selisih = $this->origin_number($request->modal_selisih);

        if (empty($uid)) {
            $paid_off = ($amount == $selisih) ? 1 : 0;

            if ($amount > $selisih) {
                return $this->ajaxResponse(false, 'Pembayaran melebihi tagihan');
            }
        } else {
            $get_old_amount = DB::table('receivable_payments')->where('uid', $uid)->where('status', 1)->first();
            $old_amount = $get_old_amount->amount;
            $selisih += $old_amount;

            $paid_off = ($amount == $selisih) ? 1 : 0;

            if ($amount > $selisih) {
                return $this->ajaxResponse(false, 'Pembayaran melebihi tagihan');
            }
        }

        try {
            DB::table('sales_orders')->where('invoice_number', $no_inv)->update(['paid_off' => $paid_off]);
        } catch (\Throwable $th) {
            return $this->ajaxResponse(false, 'Failed to save data', $th);
        }

        $count_term = DB::table('receivable_payments')->where('invoice_number', $no_inv)->where('status', 1)->count();
        $user = Auth::user();
        DB::beginTransaction();
        try {

            $data = [
                'invoice_number' => $no_inv,
                'uid_payment_method' => $request->modal_payment_method,
                'transaction_date' => Carbon::now(),
                'amount' => $amount,
                'term' => ++$count_term,
                'status' => 1
            ];

            if (!empty($uid)) {
                $data['update_at'] = Carbon::now();
                $data['update_by'] = $user->id;
            } else {
                $data['insert_at'] = Carbon::now();
                $data['insert_by'] = $user->id;
                $uid_receivable_payment = 'RP' . Carbon::now()->format('YmdHisu');
                $data['uid'] = $uid_receivable_payment;
                $data['uid_company'] = $user->uid_company;
            }


            DB::table('receivable_payments')->updateOrInsert(
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

    public function edit_receivable_payment(Request $request)
    {
        $uid = $request->uid;
        $data = DB::table('receivable_payments as rp')->join('sales_orders as so', 'so.invoice_number', '=', 'rp.invoice_number')->join('customer as c', 'c.uid', '=', 'so.uid_customer')->join('payment_method as pm', 'pm.uid', 'rp.uid_payment_method')->select('rp.uid', 'rp.invoice_number', 'c.name', 'rp.transaction_date', 'rp.term', 'rp.amount', 'rp.uid_payment_method', 'pm.name as payment_method')->where('rp.uid', $uid)->where('rp.status', 1)->get();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_receivable_payment(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();

        $get_noinv = DB::table('receivable_payments')->where('uid', $uid)->first();
        $noinv = $get_noinv->invoice_number;
        $uid_company = $get_noinv->uid_company;

        $process = DB::table('receivable_payments')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->id]);

        $get_total_bayar = DB::table('receivable_payments')->where('invoice_number', $noinv)->where('status', 1)->sum('amount');
        $get_total_inv = DB::table('sales_orders')->where('invoice_number', $noinv)->where('status', 1)->where('uid_company', $uid_company)->first();
        $paid_off = ($get_total_bayar == $get_total_inv->grand_total) ? 1 : 0;
        $update_paid_off = DB::table('sales_orders')->where('invoice_number', $noinv)->where('status', 1)->where('uid_company', $uid_company)->update(['paid_off' => $paid_off]);

        if ($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }


    public function print_pdf(Request $request)
    {
        $uid = $request->uid;
        $data['receipt'] = DB::table('receivable_payments as rp')->join('users as u', 'rp.insert_by', 'u.id')->where('rp.uid', $uid)->where('rp.status', 1)->select('rp.uid', 'rp.term', 'rp.amount', 'u.username', 'rp.invoice_number', 'rp.uid_company')->first();
        $data['company'] = DB::table('company')->where('uid', $data['receipt']->uid_company)->first();

        $data['header'] = DB::table('sales_orders as so')->join('customer as cus', 'cus.uid', 'so.uid_customer')->select('so.uid', 'so.invoice_number', 'so.uid_customer', 'so.transaction_date', 'cus.name', 'cus.phone', 'so.discount', 'so.disc_rate', 'so.tax_rate', 'so.tax_value', 'so.grand_total', 'so.collection_date', 'so.priority', 'so.paid_off', 'so.proofing')->where('so.invoice_number', $data['receipt']->invoice_number)->where('so.uid_company', $data['receipt']->uid_company)->where('so.status', 1)->first();
        $data['detail'] = DB::table('sales_order_details as pd')->join('product as p', 'p.uid', 'pd.uid_product')->join('unit as u', 'u.uid', 'pd.uid_unit')->select('pd.invoice_number', 'pd.uid_product', 'p.name as product_name', 'pd.uid_unit', 'u.name as unit_name', 'pd.qty', 'pd.price', 'pd.note', 'pd.length', 'pd.width', 'pd.packing', 'pd.cutting')->where('pd.invoice_number', $data['receipt']->invoice_number)->where('pd.status', 1)->where('pd.uid_company', $data['receipt']->uid_company)->get()->toArray();

        $pdf = PDF::loadview('transactions.receivable_payment.receipt', ['data' => $data])->setPaper('A5', 'landscape');
        return $pdf->stream('Nota-' . $data['header']->invoice_number);
        // return view('transactions.receivable_payment.receipt', ['data' => $data]);
    }

    public function export_excel()
    {
        return Excel::download(new ReceivablePaymentExport, 'Pembayaran.xlsx');
    }

    public function origin_number($number = 0)
    {
        $number = str_replace('.', '', $number);
        $number = str_replace(',', '.', $number);
        return $number;
    }

    public function get_receivable_payment(Request $request)
    {
        $uid = $request->uid;
        $data['header'] = DB::table('sales_orders as so')->join('customer as cus', 'cus.uid', 'so.uid_customer')->select('so.uid', 'so.invoice_number', 'so.uid_customer', 'so.transaction_date', 'cus.name', 'cus.phone', 'so.discount', 'so.disc_rate', 'so.tax_rate', 'so.tax_value', 'so.grand_total', 'so.collection_date', 'so.priority', 'so.uid_company', DB::raw('so.grand_total - (SELECT ifnull(sum(rp.amount), 0) FROM receivable_payments as rp WHERE rp.status = 1 AND rp.invoice_number = so.invoice_number) as selisih'))->where('so.uid', $uid)->where('so.status', 1)->first();
        $invoice_number = $data['header']->invoice_number;
        $uid_company = $data['header']->uid_company;
        $data['receipt'] = DB::table('receivable_payments as rp')->join('payment_method as pm', 'pm.uid', '=', 'rp.uid_payment_method')->select('rp.uid', 'rp.invoice_number', 'rp.amount', 'rp.transaction_date', 'pm.name as payment_method')->where('rp.invoice_number', $invoice_number)->where('rp.status', 1)->where('rp.uid_company', $uid_company)->get();

        return $this->ajaxResponse(true, 'Success!', $data);
    }


}
