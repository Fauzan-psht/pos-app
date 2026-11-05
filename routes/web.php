<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController; // <-- Tambahkan baris ini
use Illuminate\Support\Facades\Route;

// Redirect halaman awal ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Rute Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process')->middleware('throttle:5,1'); // Maks 5x per menit
});

// Rute yang butuh Login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::resource('categories', CategoryController::class)->except('show');
    
    // Tambahkan route products di sini
    Route::resource('products', ProductController::class);
});