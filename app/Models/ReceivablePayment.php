<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceivablePayment extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'receivable_payments';

    public function dataTableReceivablePayments()
    {
        $query = DB::table('receivable_payments as rp')->join('sales_orders as so', 'so.invoice_number', '=', 'rp.invoice_number')->join('customer as c', 'c.uid', '=', 'so.uid_customer')->join('payment_method as pm', 'pm.uid', '=', 'rp.uid_payment_method')->select('rp.uid', 'rp.invoice_number', 'c.name as customer_name', 'rp.transaction_date', 'rp.amount', 'rp.term', 'pm.name as payment_method')->where('rp.status', 1);

        $order = request('order')[0];
        if ($order['column'] == '0') {
            $query->orderBy('rp.uid', 'DESC');
        }

        return $query;
    }
}
