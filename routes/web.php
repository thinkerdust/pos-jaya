<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;

// Master 
use App\Http\Controllers\Master\MasterCompanyController;
use App\Http\Controllers\Master\MasterCustomerController;
use App\Http\Controllers\Master\MasterProductController;
use App\Http\Controllers\Master\MasterPaymentController;
use App\Http\Controllers\Master\MasterUnitController;
use App\Http\Controllers\Master\MasterSupplierController;
use App\Http\Controllers\Master\MasterMaterialController;

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


Route::get('/', function () { return redirect('login'); });

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::group(['middleware' => ['web', 'auth']], function() {

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

        Route::controller(UserManagementController::class)->group(function() {
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
    Route::middleware('ajax.request')->group(function() {
        Route::get('/data-company', [MasterCompanyController::class, 'list_data_company']);
        Route::get('/data-role', [UserManagementController::class, 'list_data_role']);
        Route::get('/data-payment-method', [MasterPaymentController::class, 'list_data_payment_method']);
        Route::get('/data-product', [MasterProductController::class, 'list_data_product']);
        Route::get('/data-unit', [MasterUnitController::class, 'list_data_unit']);
        Route::get('/data-supplier', [MasterSupplierController::class, 'list_data_supplier']);
        Route::get('/data-material', [MasterMaterialController::class, 'list_data_material']);
    });

    // Master Data
    Route::group(['middleware' => ["can:Menu, 'MD'"]], function() {

        // Company
        Route::group(['prefix' => 'company', 'middleware' => ["can:SubMenu, 'MD1'"]], function() {
            Route::controller(MasterCompanyController::class)->group(function() {
                Route::get('/', 'index')->name('company');
                Route::get('/datatable', 'datatable_company');
                Route::get('/add', 'add_company');
                Route::post('/store', 'store_company');
                Route::get('/edit/{uid}', 'edit_company');
                Route::get('/delete/{uid}', 'delete_company');
            });
        });

        // Product
        Route::group(['prefix' => 'product', 'middleware' => ["can:SubMenu, 'MD3'"]], function() {
            Route::controller(MasterProductController::class)->group(function() {
                Route::get('/', 'index')->name('product');
                Route::get('/datatable', 'datatable_product');
                Route::get('/add', 'add_product');
                Route::post('/store', 'store_product');
                Route::get('/edit/{uid}', 'edit_product');
                Route::get('/delete/{uid}', 'delete_product');
            });
        });

        // Payment Method
        Route::group(['prefix' => 'payment-method', 'middleware' => ["can:SubMenu, 'MD4'"]], function() {
            Route::controller(MasterPaymentController::class)->group(function() {
                Route::get('/', 'payment_method')->name('payment-method');
                Route::get('/datatable', 'datatable_payment_method');
                Route::get('/add', 'add_payment_method');
                Route::post('/store', 'store_payment_method');
                Route::get('/edit/{uid}', 'edit_payment_method');
                Route::get('/delete/{uid}', 'delete_payment_method');
            });
        });

        // Unit
        Route::group(['prefix' => 'unit', 'middleware' => ["can:SubMenu, 'MD5'"]], function() {
            Route::controller(MasterUnitController::class)->group(function() {
                Route::get('/', 'index')->name('unit');
                Route::get('/datatable', 'datatable_unit');
                Route::get('/add', 'add_unit');
                Route::post('/store', 'store_unit');
                Route::get('/edit/{uid}', 'edit_unit');
                Route::get('/delete/{uid}', 'delete_unit');
            });
        });

        // Unit
        Route::group(['prefix' => 'material', 'middleware' => ["can:SubMenu, 'MD6'"]], function() {
            Route::controller(MasterMaterialController::class)->group(function() {
                Route::get('/', 'index')->name('material');
                Route::get('/datatable', 'datatable_material');
                Route::get('/add', 'add_material');
                Route::post('/store', 'store_material');
                Route::get('/edit/{uid}', 'edit_material');
                Route::get('/delete/{uid}', 'delete_material');
            });
        });

        // Supplier
        Route::group(['prefix' => 'supplier', 'middleware' => ["can:SubMenu, 'MD7'"]], function() {
            Route::controller(MasterSupplierController::class)->group(function() {
                Route::get('/', 'index')->name('supplier');
                Route::get('/datatable', 'datatable_supplier');
                Route::get('/add', 'add_supplier');
                Route::post('/store', 'store_supplier');
                Route::get('/edit/{uid}', 'edit_supplier');
                Route::get('/delete/{uid}', 'delete_supplier');
            });
        });
        
    });

});