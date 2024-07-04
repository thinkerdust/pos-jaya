<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'purchase_orders';

    public function dataTablePurchaseOrders($min, $max, $role)
    {
        $user = Auth::user();

        $query = DB::table('purchase_orders as po')->join('supplier as sup', 'po.uid_supplier', '=', 'sup.uid')->select('po.uid', 'po.po_number', 'sup.name', DB::raw('DATE_FORMAT(po.transaction_date, "%d/%m/%Y") as transaction_date'), 'po.note', 'po.grand_total')->where('po.status', 1);
        if (!empty($min) && !empty($max)) {
            $query->whereBetween('po.transaction_date', [$min, $max]);
        }

        if ($role == 2) {
            $query->where('po.uid_company', $user->uid_company);
        } elseif ($role == 3) {
            $query->where('po.insert_by', $user->id);
        }


        $order = request('order')[0];
        if ($order['column'] == '0') {
            $query->orderBy('po.uid', 'DESC');
        }

        return $query;
    }

    public function listDataPurchaseOrders($q)
    {
        $data = DB::table('purchase_orders as po')->join('supplier as sup', 'po.uid_supplier', '=', 'sup.uid')->select('po.uid', 'po.invoice_number', 'sup.name', 'po.transaction_date', 'note', 'grand_total')->where('status', 1)->orderBy('po.uid', 'desc');
        if ($q) {
            $data = $data->where('sup.name', 'like', '%' . $q . '%');
        }
        return $data->get();
    }

}
