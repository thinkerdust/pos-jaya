<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'product';

    public function dataTableProduct()
    {
        $query = DB::table('product as p')
                    ->join('product_categories as pc', 'pc.uid', '=', 'p.uid_product_categories')
                    ->join('unit as u', 'u.uid', '=', 'p.uid_unit')
                    ->where([['p.status', 1], ['p.flag', 1], ['pc.status', 1], ['u.status', 1]])
                    ->select('p.uid', 'p.kode', 'p.name', 'p.cost_price', 'p.sell_price', 'p.retail_member_price', 'p.stock', 'p.status', 'pc.name as name_categories', 'u.name as name_unit');

        $user = Auth::user();
        if($user->id_role == 3) {
            $query->where('p.insert_by', $user->id);
        }else {
            $query->where('p.uid_company', $user->uid_company);
        }

        $order = request('order')[0];
        if($order['column'] == '0') {
            $query->orderBy('p.insert_at', 'DESC');
        }

        return $query;
    }

    public function listDataProduct($q)
    {
        $user = Auth::user();
        $data = DB::table('product')->where([['status', 1], ['uid_company', $user->uid_company]])->selectRaw("uid, CONCAT(kode, ' - ', name) as name");
        if($q) {
            $data = $data->where(function ($query) use ($q) {
                        $query->where('name', 'like', '%'.$q.'%')
                            ->orWhere('kode', 'like', '%'.$q.'%');
                    });
        }
        return $data->get();
    }

    public function editProduct($uid) 
    {
        $data = DB::table('product as p')
                    ->join('product_categories as pc', 'pc.uid', '=', 'p.uid_product_categories')
                    ->join('unit as u', 'u.uid', '=', 'p.uid_unit')
                    ->where('p.uid', $uid)
                    ->select('p.uid', 'p.kode', 'p.name', 'p.uid_product_categories', 'p.uid_unit', 'p.cost_price', 'p.sell_price', 'p.retail_member_price', 'p.stock', 'p.status', 'p.description', 'pc.name as name_categories', 'u.name as name_unit')
                    ->first();
        
        return $data;
    }

    public function dataTableProductPrice($uid_product)
    {
        $query = DB::table('product_price')
                    ->where('uid_product', $uid_product)
                    ->select('uid', 'uid_product', 'first_quantity', 'last_quantity', 'price', 'status');

        $order = request('order')[0];
        if($order['column'] == '0') {
            $query->orderBy('insert_at', 'DESC');
        }

        return $query;
    }

    public function editProductPrice($uid_product_price) 
    {
        $data = DB::table('product_price')
                    ->where('uid', $uid_product_price)
                    ->select('uid', 'uid_product', 'first_quantity', 'last_quantity', 'price')
                    ->first();

        return $data;
    }

    public function dataTableProductManualStock()
    {
        $query = DB::table('product as p')
                    ->join('product_categories as pc', 'pc.uid', '=', 'p.uid_product_categories')
                    ->join('unit as u', 'u.uid', '=', 'p.uid_unit')
                    ->where([['p.status', 1], ['p.flag', 2], ['pc.status', 1], ['u.status', 1]])
                    ->select('p.uid', 'p.kode', 'p.name', 'p.cost_price', 'p.sell_price', 'p.retail_member_price', 'p.stock', 'p.status', 'pc.name as name_categories', 'u.name as name_unit');

        $user = Auth::user();
        if($user->id_role == 3) {
            $query->where('p.insert_by', $user->id);
        }else {
            $query->where('p.uid_company', $user->uid_company);
        }

        $order = request('order')[0];
        if($order['column'] == '0') {
            $query->orderBy('p.insert_at', 'DESC');
        }

        return $query;
    }
}
