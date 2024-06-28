<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class SalesOrder extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sales_orders';

    public function dataTableSalesOrders($min, $max, $status)
    {
        $query = DB::table('sales_orders as so')->join('customer as cus', 'so.uid_customer', '=', 'cus.uid')->select('so.uid', 'so.invoice_number', 'cus.name', DB::raw('DATE_FORMAT(so.transaction_date, "%d/%m/%Y") as transaction_date'), 'so.note', 'so.grand_total', 'so.paid_off')->where('so.status', 1)->where('so.pending', 0);

        if (!empty($min) && !empty($max)) {
            $query->whereBetween('so.transaction_date', [$min, $max]);
        }

        if ($status !== null) {
            $query->where('so.paid_off', $status);
        }

        $order = request('order')[0];
        if ($order['column'] == '0') {
            $query->orderBy('so.uid', 'DESC');
        }

        return $query;
    }

    public function dataTablePending($min, $max)
    {
        $query = DB::table('sales_orders as so')->join('customer as cus', 'so.uid_customer', '=', 'cus.uid')->select('so.uid', 'so.invoice_number', 'cus.name', DB::raw('DATE_FORMAT(so.transaction_date, "%d/%m/%Y") as transaction_date'), 'so.note', 'so.grand_total')->where('so.status', 1)->where('so.pending', 1);

        if (!empty($min) && !empty($max)) {
            $query->whereBetween('so.transaction_date', [$min, $max]);
        }

        $order = request('order')[0];
        if ($order['column'] == '0') {
            $query->orderBy('so.uid', 'DESC');
        }

        return $query;
    }


    public function listDataSalesOrders($q)
    {
        $data = DB::table('sales_orders as so')->join('customer as cus', 'so.uid_customer', '=', 'cus.uid')->select('so.uid', 'so.invoice_number', 'cus.name', 'so.transaction_date', 'so.note', 'so.grand_total')->where('so.status', 1)->where('so.pending', 0);
        if ($q) {
            $data = $data->where('cus.name', 'like', '%' . $q . '%');
        }
        return $data->get();
    }

    public function listDataPending($q)
    {
        $data = DB::table('sales_orders as so')->join('customer as cus', 'so.uid_customer', '=', 'cus.uid')->select('so.uid', 'so.invoice_number', 'cus.name', 'so.transaction_date', 'so.note', 'so.grand_total')->where('so.status', 1)->where('so.pending', 1);
        if ($q) {
            $data = $data->where('cus.name', 'like', '%' . $q . '%');
        }
        return $data->get();
    }

}
