<?php

namespace App\Exports;

use App\Models\ReceivablePayment;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReceivablePaymentExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ReceivablePayment::join('sales_orders as so', 'so.invoice_number', '=', 'rp.invoice_number')->join('customer c', 'c.uid', '=', 'so.uid_customer')->join('payment_method as pm', 'pm.uid', '=', 'rp.uid_payment_method')->select('rp.invoice_number', 'c.name as customer_name', 'rp.transaction_date', 'rp.amount', 'rp.term', 'pm.name as payment_method')->where('rp.status', 1)->orderBy('rp.uid', 'desc')->get();

    }

    public function headings(): array
    {
        return ["NO INVOICE", "CUSTOMER", "TANGGAL", "JUMLAH", "TERMIN", "METODE PEMBAYARAN"];
    }
}
