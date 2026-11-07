<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = date('Y-m-d');

        // Query Ringkasan Dashboard
        $todaySales = Sale::whereDate('sale_date', $today)->where('status', 'paid')->sum('total');
        $todayTransactions = Sale::whereDate('sale_date', $today)->where('status', 'paid')->count();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->count();
        $activeProducts = Product::where('is_active', true)->count();

        // Ambil 5 Transaksi Terakhir (Baik milik Admin maupun Kasir)
        $recentSales = Sale::with(['user', 'customer'])
                            ->latest()
                            ->take(5)
                            ->get();

        return view('dashboard', compact(
            'todaySales', 'todayTransactions', 'lowStockProducts', 'activeProducts', 'recentSales'
        ));
    }
}