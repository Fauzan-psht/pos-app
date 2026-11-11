<?php

use App\Http\Controllers\ActivityLogController;
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

    // Route Kasir & Riwayat Transaksi
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);

    // Route Detail Transaksi
    Route::get('/sales/{sale}/detail', [SaleController::class, 'detail'])->name('sales.detail');

    // Route Void Transaksi (BONUS B2)
    Route::post('/sales/{sale}/void', [SaleController::class, 'void'])->name('sales.void');

    // Route Laporan Penjualan (Hanya Admin)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    
    // Route Export Excel (BONUS B1)
    Route::get('/reports/export', [ReportController::class, 'exportExcel'])->name('reports.export');

    // Route Manajemen User (Hanya Admin)
    Route::resource('users', UserController::class)->except('show');

    // Route Pengaturan Toko (Hanya Admin)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Route Log Aktivitas (BONUS B3)
    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
});