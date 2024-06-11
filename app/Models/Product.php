<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'product';

    public function dataTableProduct()
    {
        $query = DB::table('product')->select('uid', 'name', 'status');

        $order = request('order')[0];
        if($order['column'] == '0') {
            $query->orderBy('insert_at', 'DESC');
        }

        return $query;
    }

    public function listDataProduct($q)
    {
        $data = DB::table('product')->where('status', 1)->select('uid', 'name');
        if($q) {
            $data = $data->where('name', 'like', '%'.$q.'%');
        }
        return $data->get();
    }
}
