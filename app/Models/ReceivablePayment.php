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

    public function dataTableReceivablePayments($min, $max, $payment_method)
    {
        $user = Auth::user();

        $query = DB::table('receivable_payments as rp')
                    ->leftJoin('sales_orders as so', function ($join) {
                        $join->on('rp.invoice_number', '=', 'so.invoice_number');
                        $join->on('rp.uid_company', '=', 'so.uid_company');
                    })
                    ->join('customer as c', 'c.uid', '=', 'so.uid_customer')
                    ->join('payment_method as pm', 'pm.uid', '=', 'rp.uid_payment_method')
                    ->select('rp.uid', 'rp.invoice_number', 'c.name as customer_name', DB::raw('DATE_FORMAT(rp.transaction_date, "%d/%m/%Y") as transaction_date'), 'rp.amount', 'rp.term', 'pm.name as payment_method')
                    ->where('rp.status', 1);

        if (!empty($min) && !empty($max)) {
            $query->whereBetween('rp.transaction_date', [$min, $max]);
        }

        if (!empty($payment_method)) {
            $query->where('rp.uid_payment_method', $payment_method);
        }

        if ($user->id_role == 3) {
            $query->where('rp.insert_by', $user->id);
        } else {
            $query->where('rp.uid_company', $user->uid_company);
        }


        $order = request('order')[0];
        if ($order['column'] == '0') {
            $query->orderBy('rp.insert_at', 'DESC');
        }

        return $query;
    }
}
