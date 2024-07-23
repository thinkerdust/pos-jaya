<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'customer';

    public function dataTableCustomer()
    {
        $query = DB::table('customer')
                    ->where('status', 1)
                    ->select('uid', 'name', 'organisation', 'phone', 'address', 'email', 'type', 'status');

        $order = request('order')[0];
        if($order['column'] == '0') {
            $query->orderBy('insert_at', 'DESC');
        }

        return $query;
    }

    public function listDataCustomer($q)
    {
        $data = DB::table('customer')->where('status', 1)->select('uid', 'name');
        if($q) {
            $data = $data->where('name', 'like', '%'.$q.'%');
        }
        return $data->get();
    }
}
