<?php

namespace App\Exports;

use App\Models\SalesOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PendingTransactionExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return SalesOrder::join('customer as cus', 'sales_orders.uid_customer', '=', 'cus.uid')->select('sales_orders.invoice_number', 'cus.name', 'sales_orders.transaction_date', 'sales_orders.grand_total')->where('sales_orders.status', 1)->where('sales_orders.pending', 1)->orderBy('sales_orders.uid', 'desc')->get();

    }

    public function headings(): array
    {
        return ["NO INVOICE", "CUSTOMER", "TANGGAL", "GRAND TOTAL"];
    }

}
