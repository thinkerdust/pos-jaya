<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductDamage extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'product_damage';

    public function dataTableProductDamage()
    {
        $user = Auth::user();

        $query = DB::table('product_damage as pd')
                    ->join('product as p', 'pd.uid_product', '=', 'p.uid')
                    ->where([['pd.status', 1], ['p.status', 1]])
                    ->select('pd.uid', 'pd.uid_product', 'p.name as name_product', 'pd.stock', 'pd.note', 'pd.status');

        if ($user->id_role == 3) {
            $query->where('pd.insert_by', $user->id);
        } else {
            $query->where('p.uid_company', $user->uid_company);
        }

        $order = request('order')[0];
        if ($order['column'] == '0') {
            $query->orderBy('pd.insert_at', 'DESC');
        }

        return $query;
    }

    public function editProductDamage($uid)
    {
        $data = DB::table('product_damage as pd')
                    ->join('product as p', 'pd.uid_product', '=', 'p.uid')
                    ->where('pd.uid', $uid)
                    ->select('pd.uid', 'pd.uid_product', 'p.name as nama_product', 'pd.stock', 'pd.note')
                    ->first();

        return $data;
    }
}
