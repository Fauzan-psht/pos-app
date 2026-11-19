<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Helper Rupiah
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format($expression, 0, ',', '.'); ?>";
        });

        // 2. Pagination Bootstrap 5
        Paginator::useBootstrapFive();

        // 3. Kirim Data Stok Menipis ke Semua Halaman (Global)
        View::composer('*', function ($view) {
            $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();
            $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->take(5)->get();
            
            $view->with('globalLowStockCount', $lowStockCount);
            $view->with('globalLowStockProducts', $lowStockProducts);
        });
    }
}