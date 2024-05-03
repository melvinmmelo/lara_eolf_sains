<?php

use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CompanyDetailsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VehiclesController;
use App\Http\Controllers\DriversController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\PricelevelsController;
use App\Http\Controllers\PricesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\PhAddrController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentStoreController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    Route::get('/inventory', function () {
        return view('inventory');
    });

    Route::get('/inventory-items', function () {
        return view('inventory-items');
    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // views
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // backend
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // backend

    // views
    Route::get('/company', [CompanyDetailsController::class, 'index'])->name('company');
    Route::get('/edit-company', [CompanyDetailsController::class, 'edit'])->name('company.edit');
    Route::put('/edit-company/{companyDetails}', [CompanyDetailsController::class, 'update'])->name('company.update');

    Route::get('/users', [UsersController::class, 'index'])->name('users');
    Route::patch('/users', [UsersController::class, 'update'])->name('user.update');
    Route::get('/user/{id}', [UsersController::class, 'delete'])->name('user.delete');

    Route::get('/branch', [BranchesController::class, 'index'])->name('branch');
    Route::post('/branch/store', [BranchesController::class, 'store']);
    Route::get('/edit-branch/{id}', [BranchesController::class, 'edit'])->name('branch.edit');

    Route::get('/customers', [CustomersController::class, 'index'])->name('customers');
    Route::get('/customers/{id}/edit', [CustomersController::class, 'edit'])->name('customer.edit');
    Route::get('/customers/create', [CustomersController::class, 'create'])->name('customer.create');
    Route::post('/customers/store', [CustomersController::class, 'store']);
    Route::delete('/customers/{id}', [CustomersController::class, 'destroy'])->name('customer.destroy');
    // Route::put('/customers/{id}', [CustomersController::class, 'update'])->name('customer.update');
    Route::patch('/customers/', [CustomersController::class, 'update'])->name('customer.update');

    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::post('/equipment/store', [EquipmentController::class, 'store'])->name('equipment.store');
    // Route::put('/equipment/{id}', [EquipmentController::class, 'update'])->name('equipment.update');
    Route::delete('/equipment/{id}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
    Route::get('/equipment/{id}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
    Route::patch('/equipment/', [EquipmentController::class, 'update'])->name('equipment.update');


    Route::get('/get-regions', [PhAddrController::class, 'getRegions']);
    Route::get('/get-provinces/{regionId}', [PhAddrController::class, 'getProvinces']);
    Route::get('/get-cities/{provinceId}', [PhAddrController::class, 'getCities']);
    Route::get('/get-brgy/{cityId}', [PhAddrController::class, 'getBrgy']);

    Route::get('/store/create', [StoreController::class, 'create'])->name('store.create');
    Route::get('/store-info', [StoreController::class, 'index'])->name('store-info.index');
    Route::post('/store-info/store', [StoreController::class, 'store'])->name('store-info.store');
    Route::delete('/store-info/{id}', [StoreController::class, 'destroy'])->name('store-info.destroy');
    Route::patch('/store-info/update', [StoreController::class, 'update'])->name('store-info.update');

    Route::get('/equipment-store', [EquipmentStoreController::class, 'index'])->name('equipment-store.index');
    Route::get('/equipment-store/create', [EquipmentStoreController::class, 'create'])->name('equipment-store.create');
    Route::post('/equipment-storestore', [EquipmentStoreController::class, 'store'])->name('equipment-store.store');
    Route::patch('/equipment-store/update', [EquipmentStoreController::class, 'update'])->name('equistore-info.update');
    Route::delete('/equipment-store/{id}', [EquipmentStoreController::class, 'destroy'])->name('equipment-store.destroy');

    Route::get('/vehicles', [VehiclesController::class, 'index'])->name('vehicles');
    Route::get('/vehicles/{id}/edit', [VehiclesController::class, 'edit'])->name('vehicle.edit');
    Route::get('/vehicles/create', [VehiclesController::class, 'create'])->name('vehicle.create');
    Route::post('/vehicles/store', [VehiclesController::class, 'store']);
    Route::put('/vehicles/{id}', [VehiclesController::class, 'update']);
    Route::delete('/vehicles/{id}', [VehiclesController::class, 'destroy'])->name('vehicle.destroy');
    Route::patch('/vehicles/', [VehiclesController::class, 'update'])->name('vehicle.update');

    Route::get('/delivery-persons', [DriversController::class, 'index'])->name('delivery-persons');
    Route::get('/edit-delivery-person/{id}', [DriversController::class, 'edit'])->name('delivery-person.edit');
    Route::post('/Drivers/store', [DriversController::class, 'store']);

    Route::get('/pricing-level', [PriceLevelsController::class, 'index'])->name('pricing-level.index');
    Route::post('/pricing-level/store', [PriceLevelsController::class, 'store']);

    Route::get('/pricing', [PricesController::class, 'index'])->name('pricing.index');
    Route::post('/pricing/store', [PricesController::class, 'store']);


    Route::get('/product-types', [ProductTypeController::class, 'index'])->name('productType.index');
    Route::post('/product-types', [ProductTypeController::class, 'store'])->name('productType.store');

    Route::patch('/product-types', [ProductTypeController::class, 'update'])->name('productType.update');


    Route::get('/product-variants', [ProductVariantController::class, 'index'])->name('productVariant.index');
    Route::post('/product-variants', [ProductVariantController::class, 'store'])->name('productVariant.store');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');

    Route::get('/product/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('product.toggleStatus');
    Route::get('/product-type/{id}/toggle-status', [ProductTypeController::class, 'toggleStatus'])->name('productType.toggleStatus');


    Route::get('/orders', [InboundController::class, 'index'])->name('order.index');

    Route::post('/submit/process-one', [InboundController::class, 'submitProcessOne'])->name('order.submitProcessOne');

    Route::get('/ordering/{inbound}', [InboundController::class, 'orderProcessTwoUI'])->name('order.processTwo');

    // ajax
    Route::get('/productsin/{code}', [InboundController::class, 'ajaxProductList'])->name('products.ajaxProductList');
    Route::get('/inboundin/{code}', [InboundController::class, 'ajaxInboundList'])->name('inbound.inboundList');

    // update if done na mag add ng productin
    Route::post('/inbound', [InboundController::class, 'store'])->name('inbound.store');

    // update lang quantity
    // para sa add, min button
    Route::get('/inbound-updateProdQty/{inbound}/{code}/{action}', [InboundController::class, 'update'])->name('inbound.update');

    // deleting pending inbound
    Route::get('/inbound-destroy/{inbound}', [InboundController::class, 'destroy'])->name('inbound.destroy');
});

require __DIR__ . '/auth.php';
