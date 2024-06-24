<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class PurchaseOrderExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return PurchaseOrder::join('supplier as sup', 'purchase_orders.uid_supplier', '=', 'sup.uid')->select('purchase_orders.po_number', 'sup.name', 'purchase_orders.transaction_date', 'purchase_orders.grand_total')->where('purchase_orders.status', 1)->orderBy('purchase_orders.uid', 'desc')->get();

    }

    public function headings(): array
    {
        return ["NO PO", "SUPLIER", "TANGGAL", "GRAND TOTAL"];
    }
}
