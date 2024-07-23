<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasFactory;

    public function dataTablePaymentMethod()
    {
        $query = DB::table('payment_method')
                    ->where('status', 1)
                    ->select('uid', 'name', 'account_number', 'status');

        $order = request('order')[0];
        if($order['column'] == '0') {
            $query->orderBy('insert_at', 'DESC');
        }

        return $query;
    }

    public function listDataPaymentMethod($q)
    {
        $data = DB::table('payment_method')->where('status', 1)->select('uid', 'name', 'account_number');
        if($q) {
            $data = $data->where('name', 'like', '%'.$q.'%');
        }
        return $data->get();
    }
}
