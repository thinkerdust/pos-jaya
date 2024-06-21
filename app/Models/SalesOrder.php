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

    public function dataTableSalesOrders()
    {
        $query = DB::table('sales_orders as so')->join('customer as cus', 'so.uid_customer', '=', 'cus.uid')->select('so.uid', 'so.invoice_number', 'cus.name', 'so.transaction_date', 'so.note', 'so.grand_total')->where('so.status', 1)->where('so.pending', 0)->orderBy('so.uid', 'desc');

        return $query;
    }

    public function dataTablePending()
    {
        $query = DB::table('sales_orders as so')->join('customer as cus', 'so.uid_customer', '=', 'cus.uid')->select('so.uid', 'so.invoice_number', 'cus.name', 'so.transaction_date', 'so.note', 'so.grand_total')->where('so.status', 1)->where('so.pending', 1)->orderBy('so.uid', 'desc');

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
