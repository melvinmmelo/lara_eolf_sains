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
use App\Models\CompanyDetails;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // views
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // backend
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // backend

    // views
    Route::get('/company', [CompanyDetailsController::class, 'index'])->name('company');
    Route::get('/edit-company', [CompanyDetailsController::class, 'edit'])->name('company.edit');
    Route::put('/edit-company/{companyDetails}', [CompanyDetailsController::class, 'update'])->name('company.update');


    Route::get('/users', [UsersController::class, 'index'])->name('users');
    Route::patch('/users', [UsersController::class, 'update'])->name('user.update');


    Route::get('/branch', [BranchesController::class, 'index'])->name('branch');
    Route::get('/edit-branch/{id}', [BranchesController::class, 'edit'])->name('branch.edit');


    Route::get('/customers', [CustomersController::class, 'index'])->name('customers');
    Route::get('/customers/{id}/edit', [CustomersController::class, 'edit'])->name('customer.edit');
    Route::get('/customers/create', [CustomersController::class, 'create'])->name('customer.create');
    Route::post('/customers/store', [CustomersController::class, 'store']);
    Route::delete('/customers/{id}', [CustomersController::class, 'destroy'])->name('customer.destroy');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customer.update');
    Route::patch('/customers/', [CustomersController::class, 'update'])->name('customer.update');


    Route::get('/vehicles', [VehiclesController::class, 'index'])->name('vehicles');
    Route::get('/vehicles/{id}/edit', [VehiclesController::class, 'edit'])->name('vehicle.edit');
    Route::get('/vehicles/create', [VehiclesController::class, 'create'])->name('vehicle.create');
    Route::post('/vehicles/store', [VehiclesController::class, 'store']);
    Route::put('/vehicles/{id}', [VehiclesController::class, 'update']);
    Route::delete('/vehicles/{id}', [VehiclesController::class, 'destroy'])->name('vehicle.destroy');
    Route::patch('/vehicles/', [VehiclesController::class, 'update'])->name('vehicle.update');

    // Route::get('/vehicles', [VehiclesController::class, 'index'])->name('vehicles');
    // Route::get('/edit-vehicle/{id}', [CustomersController::class, 'edit'])->name('vehicle.edit');

    Route::get('/delivery-persons', [UsersController::class, 'deliveryPersons'])->name('delivery-persons');
    Route::get('/edit-delivery-person/{id}', [CustomersController::class, 'edit'])->name('delivery-person.edit');


    Route::get('/product-types', [ProductTypeController::class, 'index'])->name('productType.index');
    Route::post('/product-types', [ProductTypeController::class, 'store'])->name('productType.store');

    Route::get('/product-variants', [ProductVariantController::class, 'index'])->name('productVariant.index');
    Route::post('/product-variants', [ProductVariantController::class, 'store'])->name('productVariant.store');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');

    Route::get('/product/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('product.toggleStatus');
    Route::get('/product-type/{id}/toggle-status', [ProductTypeController::class, 'toggleStatus'])->name('productType.toggleStatus');


    Route::get('/order', [ProductController::class, 'order'])->name('order');
    Route::get('/ordering', [ProductController::class, 'ordering'])->name('ordering');



});

require __DIR__ . '/auth.php';
