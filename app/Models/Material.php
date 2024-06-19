<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Material extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'material';

    public function dataTableMaterial()
    {
        $query = DB::table('material as m')
                    ->join('unit as u', 'm.uid_unit', '=', 'u.uid')
                    ->join('supplier as s', 'm.uid_supplier', '=', 's.uid')
                    ->select('m.uid', 'm.name', 'm.status', 'm.price', 'm.stock', 'm.volume', 'u.name as unit', 's.name as supplier');

        $order = request('order')[0];
        if($order['column'] == '0') {
            $query->orderBy('m.insert_at', 'DESC');
        }

        return $query;
    }

    public function listDataMaterial($q)
    {
        $data = DB::table('material')->where('status', 1)->select('uid', 'name');
        if($q) {
            $data = $data->where('name', 'like', '%'.$q.'%');
        }
        return $data->get();
    }

    public function editMaterial($uid)
    {
        $data = DB::table('material as m')
                    ->join('unit as u', 'm.uid_unit', '=', 'u.uid')
                    ->join('supplier as s', 'm.uid_supplier', '=', 's.uid')
                    ->where('m.uid', $uid)
                    ->select('m.uid', 'm.name', 'm.status', 'm.price', 'm.stock', 'm.volume', 'm.uid_unit', 'm.uid_supplier', 'u.name as unit', 's.name as supplier', 'm.length', 'm.wide')
                    ->first();

        return $data;
    }
}
