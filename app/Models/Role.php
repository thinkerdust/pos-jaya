<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'role';
    protected $fillable = [
        'id',
        'name',
        'insert_at',
        'insert_by',
        'update_at',
        'update_by',
        'status'
    ];

    public function dataTableRole()
    {
        $query = DB::table('role')->select('id', 'name', 'status');
        return $query;
    }

    public function listDataRole($q)
    {
        $data = DB::table('role')->where('status', 1)->select('id', 'name');
        if($q) {
            $data = $data->where('name', 'like', '%'.$q.'%');
        }
        return $data->get();
    }

}
