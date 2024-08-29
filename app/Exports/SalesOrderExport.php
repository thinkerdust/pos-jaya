<?php

namespace App\Exports;

use App\Models\SalesOrder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
use Auth;


class SalesOrderExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $min;
    protected $max;
    protected $status;

    function __construct($min, $max, $status)
    {
        $this->min = $min;
        $this->max = $max;
        $this->status = $status;
    }

    public function collection()
    {
        $user = Auth::user();
        $min = $this->min;
        $max = $this->max;
        $status = $this->status;
        $role = $user->id_role;

        $query = DB::table('sales_orders as so')->join('customer as cus', 'so.uid_customer', '=', 'cus.uid')->leftJoin('sales_order_details as sod', function ($join) {
            $join->on('sod.invoice_number', '=', 'so.invoice_number');
            $join->on('sod.uid_company', '=', 'so.uid_company');
        })->select('so.invoice_number', 'cus.name', DB::raw('DATE_FORMAT(so.transaction_date, "%d/%m/%Y") as transaction_date'), DB::raw('GROUP_CONCAT( sod.note) as note'), 'so.grand_total')->where('so.status', 1)->where('so.pending', 0)->groupBy('so.invoice_number', 'so.uid_company');

        if (!empty($min) && !empty($max)) {
            $query->whereBetween('so.transaction_date', [$min, $max]);
        }

        if ($status !== null) {
            $query->where('so.paid_off', $status);
        }

        if ($role == 3) {
            $query->where('so.insert_by', $user->id);
        } else {
            $query->where('so.uid_company', $user->uid_company);
        }

        $query->orderBy(DB::raw("SUBSTRING(so.invoice_number, 3, 8)"), 'DESC')
            ->orderBy(DB::raw("CAST(SUBSTRING_INDEX(so.invoice_number, '-', -1) AS UNSIGNED)"), 'DESC');

        return $query->get();

    }

    public function headings(): array
    {
        return ["NO INVOICE", "CUSTOMER", "TANGGAL", "NOTE", "GRAND TOTAL"];
    }

}
