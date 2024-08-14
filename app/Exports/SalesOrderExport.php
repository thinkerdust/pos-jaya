<?php

namespace App\Exports;

use App\Models\SalesOrder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class SalesOrderExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return SalesOrder::join('customer as cus', 'sales_orders.uid_customer', '=', 'cus.uid')->join('company as c','sales_orders.uid_company','=','c.uid')->select('sales_orders.invoice_number', 'cus.name', 'sales_orders.transaction_date', 'sales_orders.grand_total','c.name as company')->where('sales_orders.status', 1)->where('sales_orders.pending', 0)->orderBy('sales_orders.uid', 'desc')->get();

    }

    public function headings(): array
    {
        return ["NO INVOICE", "CUSTOMER", "TANGGAL", "GRAND TOTAL","COMPANY"];
    }

}
