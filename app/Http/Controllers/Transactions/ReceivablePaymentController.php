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
        $data = $this->receivable_payment->dataTableReceivablePayments();
        return Datatables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                if (Gate::allows('crudAccess', 'TX4', $row)) {
                    $btn = '<a href="/transaction/receivale_payment/add?uid=' . $row->uid . '" class="btn btn-dim btn-outline-secondary btn-sm"><em class="icon ni ni-edit"></em><span>Edit</span></a>&nbsp;
                    <a class="btn btn-dim btn-outline-secondary btn-sm" onclick="hapus(\'' . $row->uid . '\')"><em class="icon ni ni-trash"></em><span>Delete</span></a>
                    <a class="btn btn-dim btn-outline-secondary btn-sm" target="_blank" href="/transaction/receivale_payment/receipt/' . $row->uid . '"><em class="icon ni ni-send"></em><span>Nota</span></a>';

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
        // dd($request);
        $uid = $request->input('uid_receivable_payment');

        $validator = Validator::make($request->all(), [
            'invoice_number' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->ajaxResponse(false, $validator->errors()->first());
        }

        $no_inv = $request->invoice_number;

        $count_term = DB::table('receivable_payments')->where('invoice_number', $no_inv)->where('status', 1)->count();
        $user = Auth::user();
        DB::beginTransaction();
        try {

            $data = [
                'invoice_number' => $no_inv,
                'uid_payment_method' => $request->uid_payment_method,
                'transaction_date' => Carbon::now(),
                'amount' => $request->amount,
                'priority' => $request->priority,
                'term' => ++$count_term,
                'status' => 1
            ];

            if (!empty($uid)) {
                $data['update_at'] = Carbon::now();
                $data['update_by'] = $user->username;
            } else {
                $data['insert_at'] = Carbon::now();
                $data['insert_by'] = $user->username;
                $uid_receivable_payment = 'RP' . Carbon::now()->format('YmdHisu');
                $data['uid'] = $uid_receivable_payment;
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
        $data = DB::table('receivable_payments as rp')->join('sales_orders as so', 'so.invoice_number', '=', 'rp.invoice_number')->join('customer c', 'c.uid', '=', 'so.uid_customer')->select('rp.uid', 'rp.invoice_number', 'c.name', 'rp.transaction_date', 'rp.term', 'rp.amount', 'rp.uid_payment_method')->where('rp.uid', $uid)->get();
        return $this->ajaxResponse(true, 'Success!', $data);
    }

    public function delete_receivable_payment(Request $request)
    {
        $uid = $request->uid;
        $user = Auth::user();
        $process = DB::table('receivable_payments')->where('uid', $uid)
            ->update(['status' => 0, 'update_at' => Carbon::now(), 'update_by' => $user->username]);


        if ($process) {
            return $this->ajaxResponse(true, 'Data save successfully');
        } else {
            return $this->ajaxResponse(false, 'Failed to save data');
        }
    }


    public function print_pdf(Request $request)
    {
        $uid = $request->uid;
        $data['header'] = DB::table('sales_orders as so')->join('customer as cus', 'cus.uid', 'so.uid_customer')->select('so.uid', 'so.invoice_number', 'so.uid_customer', 'so.transaction_date', 'cus.name', 'cus.phone', 'so.discount', 'so.disc_rate', 'so.tax_rate', 'so.tax_value', 'so.grand_total', 'so.collection_date', 'so.priority')->where('so.uid', $uid)->first();
        $data['detail'] = DB::table('sales_order_details as pd')->join('product as p', 'p.uid', 'pd.uid_product')->join('unit as u', 'u.uid', 'pd.uid_unit')->select('pd.invoice_number', 'pd.uid_product', 'p.name as product_name', 'pd.uid_unit', 'u.name as unit_name', 'pd.qty', 'pd.price')->where('pd.invoice_number', $data['header']->invoice_number)->get()->toArray();
        $data['receipt'] = DB::table('receivable_payments as rp')->where('rp.invoice_number', $data['header']->invoice_number)->where('status', 1)->sum('amount');

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

}
