<?php

use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CompanyDetailsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
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

    Route::get('/branch', [BranchesController::class, 'index'])->name('branch');
    Route::get('/edit-branch/{id}', [BranchesController::class, 'edit'])->name('branch.edit');

    Route::get('/customers', [CustomersController::class, 'index'])->name('customers');
    Route::get('/customer/create', [CustomersController::class, 'create'])->name('customer.create');
    Route::get('/edit-customer/{id}', [CustomersController::class, 'edit'])->name('customer.edit');

    Route::get('/vehicles', [CustomersController::class, 'index'])->name('vehicles');
    Route::get('/edit-vehicle/{id}', [CustomersController::class, 'edit'])->name('vehicle.edit');

    Route::get('/delivery-person', [CustomersController::class, 'index'])->name('branch');
    Route::get('/edit-delivery-person/{id}', [CustomersController::class, 'edit'])->name('branch.edit');
});

require __DIR__.'/auth.php';
