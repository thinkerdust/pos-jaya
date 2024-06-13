<?php

use App\Http\Controllers\Transactions\PurchaseController;
use App\Http\Controllers\Transactions\SalesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Master\MasterCompanyController;
use App\Http\Controllers\Master\MasterMemberController;
use App\Http\Controllers\Master\MasterProductController;
use App\Http\Controllers\Master\MasterPaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return redirect('login');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::group(['middleware' => ['web', 'auth']], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(AuthController::class)->group(function () {
        Route::get('/user-authenticate', 'user_authenticate');
        Route::get('/change-password', 'change_password');
        Route::post('/process-change-password', 'process_change_password');
    });

    Route::middleware("can:Menu, 'UM'")->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('/reset-password/{id}', 'reset_password');
            Route::get('/user-activation/{id}', 'user_activation');
            Route::post('/register', 'register')->name('register');
            Route::get('/edit-user/{id}', 'edit_user');
        });

        Route::controller(UserManagementController::class)->group(function () {
            // user
            Route::get('/user-management', 'index')->middleware("can:SubMenu, 'UM1'");
            Route::get('/datatable-user-management', 'datatable_user_management');

            // menu
            Route::get('/menu', 'menu')->middleware("can:SubMenu, 'UM2'");
            Route::get('/datatable-menu', 'datatable_menu');
            Route::post('/store-menu', 'store_menu');
            Route::get('/edit-menu/{id}', 'edit_menu');
            Route::get('/delete-menu/{id}', 'delete_menu');

            // role
            Route::get('/role', 'role')->middleware("can:SubMenu, 'UM3'");
            Route::get('/datatable-role', 'datatable_role');
            Route::get('/list-permissions-menu', 'list_permissions_menu');
            Route::post('/store-role', 'store_role');
            Route::get('/edit-role/{id}', 'edit_role');
        });
    });

    // Master Data
    Route::middleware('ajax.request')->group(function () {
        Route::get('/data-company', [MasterCompanyController::class, 'list_data_company']);
        Route::get('/data-role', [UserManagementController::class, 'list_data_role']);
    });

    // Master Data
    Route::group(['middleware' => ["can:Menu, 'MD'"]], function () {

        Route::group(['prefix' => 'company', 'middleware' => ["can:SubMenu, 'MD1'"]], function () {
            Route::controller(MasterCompanyController::class)->group(function () {
                // Company
                Route::get('/', 'index')->name('company');
                Route::get('/datatable', 'datatable_company');
                Route::get('/add', 'add_company');
                Route::post('/store', 'store_company');
                Route::get('/edit/{uid}', 'edit_company');
                Route::get('/delete/{uid}', 'delete_company');
            });
        });

    });


    // Transactions
    Route::group(['prefix' => 'transaction', 'middleware' => ["can:Menu, 'TX'"]], function () {

        Route::group(['prefix' => 'purchase', 'middleware' => ["can:SubMenu, 'TX1'"]], function () {
            Route::controller(PurchaseController::class)->group(function () {
                // Company
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable_purchase_order');
                Route::get('/add', 'add_purchase_order');
                Route::post('/store', 'store_purchase_order');
                Route::get('/edit/{uid}', 'edit_purchase_order');
                Route::get('/delete/{uid}', 'delete_purchase_order');
            });
        });

    });

    Route::group(['prefix' => 'transaction', 'middleware' => ["can:Menu, 'TX'"]], function () {

        Route::group(['prefix' => 'sales', 'middleware' => ["can:SubMenu, 'TX2'"]], function () {
            Route::controller(SalesController::class)->group(function () {
                // Company
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable_sales_order');
                Route::get('/add', 'add_sales_order');
                Route::post('/store', 'store_sales_order');
                Route::get('/edit/{uid}', 'edit_sales_order');
                Route::get('/delete/{uid}', 'delete_sales_order');
            });
        });

    });



});