<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
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
    
    // Route Stok Masuk
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

    // Route Kasir & Riwayat Transaksi (Hanya ditulis SEKALI)
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);

    // Route Laporan Penjualan (Hanya Admin)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Route Export Excel (BONUS B1) <-- TAMBAHAN BARU
    Route::get('/reports/export', [ReportController::class, 'exportExcel'])->name('reports.export');

    // Route Manajemen User (Hanya Admin)
    Route::resource('users', UserController::class)->except('show');

    // Route Pengaturan Toko (Hanya Admin)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});