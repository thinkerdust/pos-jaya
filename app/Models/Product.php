<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'product';

    public function dataTableProduct()
    {
        $query = DB::table('product as p')
                    ->join('material as m', 'm.uid', '=', 'p.uid_material')
                    ->join('unit as u', 'u.uid', '=', 'p.uid_unit')
                    ->select('p.uid', 'p.name', 'p.status', 'm.name as name_material', 'u.name as name_unit');

        $order = request('order')[0];
        if($order['column'] == '0') {
            $query->orderBy('p.insert_at', 'DESC');
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

    public function editProduct($uid) 
    {
        $data = DB::table('product as p')
                ->join('material as m', 'm.uid', '=', 'p.uid_material')
                ->join('unit as u', 'u.uid', '=', 'p.uid_unit')
                ->where('p.uid', $uid)
                ->select('p.uid', 'p.name', 'p.status', 'p.uid_material', 'p.uid_unit', 'm.name as name_material', 'u.name as name_unit')
                ->first();
        
        return $data;
    }
}
