<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use Auth;
class PurchaseOrderExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $min;
    protected $max;

    function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
    }


    public function collection()
    {
        $user = Auth::user();
        $role = $user->id_role;
        $query = DB::table('purchase_orders as po')->join('supplier as sup', 'po.uid_supplier', '=', 'sup.uid')->select('po.po_number', 'sup.name', DB::raw('DATE_FORMAT(po.transaction_date, "%d/%m/%Y") as transaction_date'), 'po.note', 'po.grand_total')->where('po.status', 1);
        if (!empty($this->min) && !empty($this->max)) {
            $query->whereBetween('po.transaction_date', [$this->min, $this->max]);
        }

        if ($role == 3) {
            $query->where('po.insert_by', $user->id);
        } else {
            $query->where('po.uid_company', $user->uid_company);
        }

        $query->orderBy(DB::raw("SUBSTRING(po.po_number, 3, 8)"), 'DESC')
            ->orderBy(DB::raw("CAST(SUBSTRING_INDEX(po.po_number, '-', -1) AS UNSIGNED)"), 'DESC');

        return $query->get();

    }

    public function headings(): array
    {
        return ["NO PO", "SUPLIER", "TANGGAL", "NOTE", "GRAND TOTAL"];
    }
}
