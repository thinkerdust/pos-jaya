<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function dataTableUser()
    {
        $query = DB::table('users as u')
                    ->join('role as r', 'u.id_role', '=', 'r.id')
                    ->where('r.status', 1)
                    ->select('u.id', 'u.username', 'u.name', 'u.phone', 'u.status', 'u.email', 'r.name as roles');

        return $query;
    }

    public function editUser($id)
    {
        $data = DB::table('users as u')
                    ->join('role as r', 'u.id_role', '=', 'r.id')
                    ->join('company as c', 'u.uid_company', '=', 'c.uid')
                    ->where('u.id', $id)
                    ->select('u.id', 'u.username', 'u.name', 'u.phone', 'u.status', 'u.email', 'u.uid_company', 'u.id_role', 'r.name as name_role', 'c.name as name_company')
                    ->first();

        return $data;
    }
}
