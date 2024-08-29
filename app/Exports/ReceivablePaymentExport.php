<?php

namespace App\Exports;

use App\Models\ReceivablePayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use Auth;
class ReceivablePaymentExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $min;
    protected $max;
    protected $payment_method;

    function __construct($min, $max, $payment_method)
    {
        $this->min = $min;
        $this->max = $max;
        $this->payment_method = $payment_method;
    }


    public function collection()
    {
        $user = Auth::user();
        $min = $this->min;
        $max = $this->max;
        $payment_method = $this->payment_method;
        $role = $user->id_role;
        $query = DB::table('receivable_payments as rp')->leftJoin('sales_orders as so', function ($join) {
            $join->on('rp.invoice_number', '=', 'so.invoice_number');
            $join->on('rp.uid_company', '=', 'so.uid_company');
        })->join('customer as c', 'c.uid', '=', 'so.uid_customer')->join('payment_method as pm', 'pm.uid', '=', 'rp.uid_payment_method')->select('rp.uid', 'rp.invoice_number', 'c.name as customer_name', DB::raw('DATE_FORMAT(rp.transaction_date, "%d/%m/%Y") as transaction_date'), 'rp.amount', 'rp.term', 'pm.name as payment_method')->where('rp.status', 1);
        if (!empty($min) && !empty($max)) {
            $query->whereBetween('rp.transaction_date', [$min, $max]);
        }

        if (!empty($payment_method)) {
            $query->where('rp.uid_payment_method', $payment_method);
        }

        if ($role == 3) {
            $query->where('rp.insert_by', $user->id);
        } else {
            $query->where('rp.uid_company', $user->uid_company);
        }


        $query->orderBy('rp.insert_at', 'DESC');
        return $query->get();
    }

    public function headings(): array
    {
        return ["NO INVOICE", "CUSTOMER", "TANGGAL", "JUMLAH", "TERMIN", "METODE PEMBAYARAN", "COMPANY"];
    }
}
