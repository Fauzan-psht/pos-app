<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = date('Y-m-d');

        // Query Ringkasan Dashboard
        $todaySales = Sale::whereDate('sale_date', $today)->where('status', 'paid')->sum('total');
        $todayTransactions = Sale::whereDate('sale_date', $today)->where('status', 'paid')->count();
        $lowStockProducts = Product::where('stock', '<=', DB::raw('min_stock'))->count();
        $activeProducts = Product::where('is_active', true)->count();

        return view('dashboard', compact(
            'todaySales', 'todayTransactions', 'lowStockProducts', 'activeProducts'
        ));
    }
}