<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController; // <-- Tambahan untuk Kasir & Riwayat
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Redirect halaman awal ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Rute Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process')->middleware('throttle:5,1');
});

// Rute yang butuh Login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Resource Routes
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class)->except('show');
    
    // Route Stok Masuk (Hanya index, create, store, show)
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

    // Route Kasir & Riwayat Transaksi (Hanya index, create, store, show)
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
});