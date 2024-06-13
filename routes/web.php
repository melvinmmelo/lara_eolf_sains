<?php

use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CompanyDetailsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DeliveryPurchaseReceiptController;
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
use App\Http\Controllers\ItemMasterDataController;
use App\Http\Controllers\addbadorderController;
use App\Http\Controllers\TempBadOrderController;
use App\Http\Controllers\BadOrderController;
Route::get('/', function () {
    if (auth()->check()) {

        if (session('branch_code') == null) {
            return redirect()->route('branch-select');
        } else {
            return redirect()->route('dashboard');
        }
    }

    return view('auth.login');
});

Route::get('/bad-orders-list', function () {
    return view('badorder');
});

// Route::get('/addbad-orders', function () {
//     return view('addbadorder');
// });

Route::get('/deliveryreceipt', function () {
    return view('deliveryreceipt');
});

Route::get('/bad-orders', [addbadorderController::class, 'create'])->name('addbadorder.create');
Route::get('/api/getCustomerItems/{customerId}', [addbadorderController::class, 'getCustomerItems']);
Route::get('/fetch-items', [addbadorderController::class, 'fetchItems'])->name('fetch.items');


// Route::get('/bad-orders', [addbadorderController::class, 'create'])->name('addbadorder.create');
// // Route::post('/save-bad-orders', [addbadorderController::class, 'save'])->name('save-bad-orders');
// Route::post('/save-bad-orders', [addbadorderController::class, 'store'])->name('addbadorder.store');

Route::get('/get-products/{inboundId}/{customerId}', [addbadorderController::class, 'getProducts']);


Route::get('/bad-orders', [addbadorderController::class, 'create'])->name('addbadorder.create');
Route::post('/bad-orders', [addbadorderController::class, 'store'])->name('addbadorder.store');
// Route::get('/get-products/{inboundId}/{customerId}', [addbadorderController::class, 'getProducts'])->name('getProducts');



Route::get('/bad-orders-list', [BadOrderController::class, 'index'])->name('badOrders.index');



Route::post('/save-temp-bad-order', [TempBadOrderController::class, 'store']);
Route::post('/clear-temp-bad-orders', [TempBadOrderController::class, 'clear']);

Route::get('/delivery-receipt', function () {
    return view('delivery-receipt');
});

Route::middleware('auth')->group(function () {

    Route::get('/inventory', [DeliveryPurchaseReceiptController::class, 'index'])->name('delivery-purchase-receipts.index');

    Route::post('/delivery-purchase-receipts', [DeliveryPurchaseReceiptController::class, 'store'])->name('delivery-purchase-receipts.store');

    Route::get('/dpr-products/{dprId}', [DeliveryPurchaseReceiptController::class, 'products'])->name('drp.products');

    Route::post('/dpr-product/store', [DeliveryPurchaseReceiptController::class, 'storeProduct'])->name('dpr-product.store');

    Route::get('/dpr-save/{id}', [DeliveryPurchaseReceiptController::class, 'saveAndInventoryProduct'])->name('dpr.save');

    Route::get('/dpr-delete/{drid}/{pcode}', [DeliveryPurchaseReceiptController::class, 'delete'])->name('dpr.delete');

    Route::get('/dpr-delete/{drid}/{pcode}', [DeliveryPurchaseReceiptController::class, 'delete'])->name('dpr.delete');

    Route::post('/dpr-hold', [DeliveryPurchaseReceiptController::class, 'holdProduct'])->name('dpr.holdProduct');


    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/inventory-items', function () {
        return view('inventory-items');
    });

    Route::get('/loading-ticket', function () {
        return view('loading-ticket');
    });

    // Route::get('/customersinfo', function () {
    //     return view('customersinfo');
    // });

    Route::get('/branch-select', function () {
        return view('branch-select');
    })->name('branch-select');


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

    // Route::get('/customers', [CustomersController::class, 'index'])->name('customers');
    // Route::get('/customers/{id}/edit', [CustomersController::class, 'edit'])->name('customer.edit');
    // Route::get('/customers/create', [CustomersController::class, 'create'])->name('customer.create');
    // Route::post('/customers/store', [CustomersController::class, 'store'])->name('customers.store'); // Corrected route definition
    // Route::delete('/customers/{id}', [CustomersController::class, 'destroy'])->name('customer.destroy');

    // Route::patch('/customers/', [CustomersController::class, 'update'])->name('customer.update');
    // Route::delete('/customer/{customer}/store/{store}', [CustomerController::class, 'destroy'])->name('customer.destroy');

    Route::get('/customers', [CustomersController::class, 'index'])->name('customers');
    Route::get('/customers/{id}/edit', [CustomersController::class, 'edit'])->name('customer.edit');
    Route::get('/customers/create', [CustomersController::class, 'create'])->name('customer.create');
    Route::post('/customers/store', [CustomersController::class, 'store'])->name('customers.store');
    Route::delete('/customers/{id}', [CustomersController::class, 'destroy'])->name('customer.destroy');
    Route::patch('/customers/', [CustomersController::class, 'update'])->name('customer.update');
    Route::delete('/customers/{customer}/store/{store}', [CustomersController::class, 'destroyStore'])->name('customer.store.destroy');

    // Route::get('/customersinfo', [CustomersController::class, 'index'])->name('customersinfo');

    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::post('/equipment/store', [EquipmentController::class, 'store'])->name('equipment.store');
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
    Route::post('/equipment-store/update-pull-status', [EquipmentStoreController::class, 'updatePullStatus'])->name('equipment-store.updatePullStatus');


    Route::get('/vehicles', [VehiclesController::class, 'index'])->name('vehicles');
    Route::get('/vehicles/{id}/edit', [VehiclesController::class, 'edit'])->name('vehicle.edit');
    Route::get('/vehicles/create', [VehiclesController::class, 'create'])->name('vehicle.create');
    Route::post('/vehicles/store', [VehiclesController::class, 'store']);
    Route::put('/vehicles/{id}', [VehiclesController::class, 'update']);
    Route::delete('/vehicles/{id}', [VehiclesController::class, 'destroy'])->name('vehicle.destroy');
    Route::patch('/vehicles/', [VehiclesController::class, 'update'])->name('vehicle.update');

    Route::get('/delivery-persons', [DriversController::class, 'index'])->name('delivery-persons');
    Route::get('/edit-delivery-person/{id}', [DriversController::class, 'edit'])->name('delivery-person.edit');
    Route::put('/dp/update', [DriversController::class, 'update'])->name('delivery-person.update');

    Route::get('/dp-details/{id}', [DriversController::class, 'getDetails'])->name('dp.details');


    Route::post('/Drivers/store', [DriversController::class, 'store']);

    Route::get('/pricing-level', [PriceLevelsController::class, 'index'])->name('pricing-level.index');
    Route::post('/pricing-level/store', [PriceLevelsController::class, 'store']);

    Route::put('/pricing-level/update', [PriceLevelsController::class, 'update'])->name('pricing-level.update');


    Route::get('/pricing', [PricesController::class, 'index'])->name('pricing.index');
    Route::post('/pricing/store', [PricesController::class, 'store']);
    Route::patch('/pricing/update', [PricesController::class, 'update'])->name('price.update');


    Route::get('/product-types', [ProductTypeController::class, 'index'])->name('productType.index');
    Route::post('/product-types', [ProductTypeController::class, 'store'])->name('productType.store');

    Route::patch('/product-types', [ProductTypeController::class, 'update'])->name('productType.update');

    Route::get('/product-variants', [ProductVariantController::class, 'index'])->name('productVariant.index');
    Route::post('/product-variants', [ProductVariantController::class, 'store'])->name('productVariant.store');

    Route::patch('/product-variants', [ProductVariantController::class, 'update'])->name('productVariant.update');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');

    Route::get('/product/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('product.toggleStatus');
    Route::get('/product-type/{id}/toggle-status', [ProductTypeController::class, 'toggleStatus'])->name('productType.toggleStatus');

    Route::get('/orders', [InboundController::class, 'index'])->name('order.index');

    Route::post('/submit/process-one', [InboundController::class, 'submitProcessOne'])->name('order.submitProcessOne');

    Route::get('/ordering/{inbound}', [InboundController::class, 'orderProcessTwoUI'])->name('order.processTwo');

    // ajax
    Route::get('/productsin/{code}', [InboundController::class, 'ajaxProductList'])->name('products.ajaxProductList');
    Route::get('/inboundin/{code}/{qty}', [InboundController::class, 'ajaxInboundList'])->name('inbound.inboundList');
    Route::get('/delete-inboundin/{inboundId}/{pcode}', [InboundController::class, 'deleteAInbound'])->name('inbound.deleteAInbound');

    // update if done na mag add ng productin
    Route::post('/inbound', [InboundController::class, 'store'])->name('inbound.store');

    Route::patch('/inbound/add-payment', [InboundController::class, 'addPayment'])->name('inbound.addPayment');


    // update lang quantity
    // para sa add, min button
    Route::get('/inbound-updateProdQty/{inbound}/{code}/{action}', [InboundController::class, 'update'])->name('inbound.update');

    // deleting pending inbound
    Route::get('/inbound-destroy/{inbound}', [InboundController::class, 'destroy'])->name('inbound.destroy');

    Route::get('/set-branch/{code}', [BranchesController::class, 'setBranchSession'])->name('branch.setBranchSession');

    Route::get('/item-master-data', [ItemMasterDataController::class, 'index'])->name('itemdata.index');

    Route::put('/branch', [BranchesController::class, 'update'])->name('branch.update');


});

require __DIR__ . '/auth.php';
require __DIR__ . '/ajaxreq.php';
