<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'company';

    public function dataTableCompany()
    {
        $query = DB::table('company')->select('uid', 'name', 'address', 'phone', 'status');

        $user = Auth::user();
        if(!in_array($user->id_role, [1,2])) {
            $query->where('uid', $user->uid_company);
        }

        return $query;
    }

    public function listDataCompany($q)
    {
        $data = DB::table('company')->where('status', 1)->select('uid', 'name');
        if($q) {
            $data = $data->where('name', 'like', '%'.$q.'%');
        }
        return $data->get();
    }
}
