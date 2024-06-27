<?php

use App\Http\Controllers\Transactions\PurchaseController;
use App\Http\Controllers\Transactions\SalesController;
use App\Http\Controllers\Transactions\ReceivablePaymentController;
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
use App\Http\Controllers\Master\ProductCategoriesController;

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

    Route::group(['prefix' => 'dashboard', 'middleware' => ['ajax.request']], function() {
        Route::controller(DashboardController::class)->group(function () {
            Route::post('/total-products', 'total_products');
            Route::post('/total-purchase', 'total_purchase');
            Route::post('/total-sales', 'total_sales');
            Route::post('/purchase-statistics', 'purchase_statistics');
            Route::post('/sales-statistics', 'sales_statistics');
        });
    });

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
        Route::get('/data-payment-method', [MasterPaymentController::class, 'list_data_payment_method']);
        Route::get('/data-product', [MasterProductController::class, 'list_data_product']);
        Route::get('/data-unit', [MasterUnitController::class, 'list_data_unit']);
        Route::get('/data-supplier', [MasterSupplierController::class, 'list_data_supplier']);
        Route::get('/data-material', [MasterMaterialController::class, 'list_data_material']);
        Route::get('/data-customer', [MasterCustomerController::class, 'list_data_customer']);
        Route::get('/data-product-categories', [ProductCategoriesController::class, 'list_data_product_categories']);
    });

    // Master Data
    Route::group(['middleware' => ["can:Menu, 'MD'"]], function () {
        // Company
        Route::group(['prefix' => 'company', 'middleware' => ["can:SubMenu, 'MD1'"]], function () {
            Route::controller(MasterCompanyController::class)->group(function () {
                Route::get('/', 'index')->name('company');
                Route::get('/datatable', 'datatable_company');
                Route::get('/add', 'add_company');
                Route::post('/store', 'store_company');
                Route::get('/edit/{uid}', 'edit_company');
                Route::get('/delete/{uid}', 'delete_company');
            });
        });

        Route::group(['prefix' => 'customer', 'middleware' => ["can:SubMenu, 'MD2'"]], function () {
            Route::controller(MasterCustomerController::class)->group(function () {
                Route::get('/', 'index')->name('customer');
                Route::get('/datatable', 'datatable_customer');
                Route::post('/store', 'store_customer');
                Route::get('/edit/{uid}', 'edit_customer');
                Route::get('/delete/{uid}', 'delete_customer');
            });
        });

        // Product
        Route::group(['prefix' => 'product', 'middleware' => ["can:SubMenu, 'MD3'"]], function () {
            Route::controller(MasterProductController::class)->group(function () {
                Route::get('/', 'index')->name('product');
                Route::get('/datatable', 'datatable_product');
                Route::post('/store', 'store_product');
                Route::get('/edit/{uid}', 'edit_product');
                Route::get('/delete/{uid}', 'delete_product');
                Route::post('/datatable-price', 'datatable_product_price');
                Route::post('/store-price', 'store_product_price');
                Route::get('/edit-price/{uid}', 'edit_product_price');
                Route::get('/delete-price/{uid}', 'delete_product_price');
                Route::get('/get-price/{uid}', 'get_grosir_price');
            });
        });

        // Payment Method
        Route::group(['prefix' => 'payment-method', 'middleware' => ["can:SubMenu, 'MD4'"]], function () {
            Route::controller(MasterPaymentController::class)->group(function () {
                Route::get('/', 'payment_method')->name('payment-method');
                Route::get('/datatable', 'datatable_payment_method');
                Route::post('/store', 'store_payment_method');
                Route::get('/edit/{uid}', 'edit_payment_method');
                Route::get('/delete/{uid}', 'delete_payment_method');
            });
        });

        // Unit
        Route::group(['prefix' => 'unit', 'middleware' => ["can:SubMenu, 'MD5'"]], function () {
            Route::controller(MasterUnitController::class)->group(function () {
                Route::get('/', 'index')->name('unit');
                Route::get('/datatable', 'datatable_unit');
                Route::post('/store', 'store_unit');
                Route::get('/edit/{uid}', 'edit_unit');
                Route::get('/delete/{uid}', 'delete_unit');
            });
        });

        // Unit
        Route::group(['prefix' => 'material', 'middleware' => ["can:SubMenu, 'MD6'"]], function () {
            Route::controller(MasterMaterialController::class)->group(function () {
                Route::get('/', 'index')->name('material');
                Route::get('/datatable', 'datatable_material');
                Route::post('/store', 'store_material');
                Route::get('/edit/{uid}', 'edit_material');
                Route::get('/delete/{uid}', 'delete_material');
            });
        });

        // Supplier
        Route::group(['prefix' => 'supplier', 'middleware' => ["can:SubMenu, 'MD7'"]], function () {
            Route::controller(MasterSupplierController::class)->group(function () {
                Route::get('/', 'index')->name('supplier');
                Route::get('/datatable', 'datatable_supplier');
                Route::post('/store', 'store_supplier');
                Route::get('/edit/{uid}', 'edit_supplier');
                Route::get('/delete/{uid}', 'delete_supplier');
            });
        });

        // Product Categories
        Route::group(['prefix' => 'product-categories', 'middleware' => ["can:SubMenu, 'MD8'"]], function () {
            Route::controller(ProductCategoriesController::class)->group(function () {
                Route::get('/', 'index')->name('product-categories');
                Route::get('/datatable', 'datatable_product_categories');
                Route::post('/store', 'store_product_categories');
                Route::get('/edit/{uid}', 'edit_product_categories');
                Route::get('/delete/{uid}', 'delete_product_categories');
            });
        });

    });

    // Transactions
    Route::group(['prefix' => 'transaction', 'middleware' => ["can:Menu, 'TX'"]], function () {

        Route::group(['prefix' => 'purchase', 'middleware' => ["can:SubMenu, 'TX1'"]], function () {
            Route::controller(PurchaseController::class)->group(function () {
                // Purchase Order
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable_purchase_order');
                Route::get('/add', 'add_purchase_order');
                Route::post('/store', 'store_purchase_order');
                Route::get('/edit/{uid}', 'edit_purchase_order');
                Route::get('/delete/{uid}', 'delete_purchase_order');
                Route::get('/export_excel', 'export_excel');
                Route::post('/check_stock', 'check_stock');
            });
        });

        Route::group(['prefix' => 'sales', 'middleware' => ["can:SubMenu, 'TX2'"]], function () {
            Route::controller(SalesController::class)->group(function () {
                // Sales Order
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable_sales_order');
                Route::get('/add', 'add_sales_order');
                Route::post('/store', 'store_sales_order');
                Route::get('/edit/{uid}', 'edit_sales_order');
                Route::get('/delete/{uid}', 'delete_sales_order');
                Route::get('/invoice/{uid}', 'print_pdf');
                Route::get('/export_excel', 'export_excel');
                Route::get('/export_excel_pending', 'export_excel_pending');
                Route::post('/check_stock', 'check_stock');
            });
        });

        Route::group(['prefix' => 'pending', 'middleware' => ["can:SubMenu, 'TX3'"]], function () {
            Route::controller(SalesController::class)->group(function () {
                // Pending
                Route::get('/', 'pending');
                Route::get('/datatable', 'datatable_pending');
            });
        });


        Route::group(['prefix' => 'receivable_payment', 'middleware' => ["can:SubMenu, 'TX4'"]], function () {
            Route::controller(ReceivablePaymentController::class)->group(function () {
                // Purchase Order
                Route::get('/', 'index');
                Route::get('/datatable', 'datatable_receivable_payment');
                Route::get('/add', 'add_receivable_payment');
                Route::post('/store', 'store_receivable_payment');
                Route::get('/edit/{uid}', 'edit_receivable_payment');
                Route::get('/delete/{uid}', 'delete_receivable_payment');
                Route::get('/receipt/{uid}', 'print_pdf');
                Route::get('/export_excel', 'export_excel');
                Route::get('/get_data_receipt/{uid}', 'get_receivable_payment');
            });
        });



    });


});