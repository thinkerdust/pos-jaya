<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use DB;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        #==================== Gate CRUD ====================
        Gate::define('crudAccess', function ($user, $id_menu) {
            $db = DB::select(
                "select a.code_menu from access_role a
                join users u on u.id_role = a.id_role
                where a.flag_access = 1 and u.id_role = $user->id_role and a.code_menu = '$id_menu'"
            );
            return $db;
        });

        #====================  Menu ====================
        Gate::define('Menu', function ($user, $kd_parent) {
            
            $db = DB::select(
                    "select a.code_menu from access_role a
                    join users u on u.id_role = a.id_role
                    where a.flag_access <> 9 and u.id_role = $user->id_role and LEFT(a.code_menu, 2) = '$kd_parent'"
                );
            return $db;
        });

        #==================== Sub Menu ====================
        Gate::define('SubMenu', function ($user, $code_menu) {
            $db = DB::select(
                "select a.code_menu from access_role a
                join users u on u.id_role = a.id_role
                where u.id_role = $user->id_role and a.code_menu = '$code_menu' and a.flag_access <> 9"
            );
            return $db;
        });
    }
}
