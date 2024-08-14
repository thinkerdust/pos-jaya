<?php

namespace App\Exports;

use App\Models\ReceivablePayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReceivablePaymentExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ReceivablePayment::leftJoin('sales_orders as so', function ($join) {
            $join->on('receivable_payments.invoice_number', '=', 'so.invoice_number');
            $join->on('receivable_payments.uid_company', '=', 'so.uid_company');
        })->join('customer as c', 'c.uid', '=', 'so.uid_customer')->join('payment_method as pm', 'pm.uid', '=', 'receivable_payments.uid_payment_method')->join('company as com','receivable_payments.uid_company','=','com.uid')->select('receivable_payments.invoice_number', 'c.name as customer_name', 'receivable_payments.transaction_date', 'receivable_payments.amount', 'receivable_payments.term', 'pm.name as payment_method','com.name as company')->where('receivable_payments.status', 1)->orderBy('receivable_payments.uid', 'desc')->get();

    }

    public function headings(): array
    {
        return ["NO INVOICE", "CUSTOMER", "TANGGAL", "JUMLAH", "TERMIN", "METODE PEMBAYARAN","COMPANY"];
    }
}
