<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductCategories extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'product_categories';

    public function dataTableProductCategories()
    {
        $query = DB::table('product_categories')->select('uid', 'name', 'status');

        $order = request('order')[0];
        if($order['column'] == '0') {
            $query->orderBy('insert_at', 'DESC');
        }

        return $query;
    }

    public function listDataProductCategories($q)
    {
        $data = DB::table('product_categories')->where('status', 1)->select('uid', 'name');
        if($q) {
            $data = $data->where('name', 'like', '%'.$q.'%');
        }
        return $data->get();
    }
}
