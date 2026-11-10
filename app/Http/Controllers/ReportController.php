<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport; // <-- Tambahan untuk Export Excel
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel; // <-- Tambahan untuk Export Excel

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin')];
    }

    public function index(Request $request)
    {
        // Default filter tanggal: Awal bulan ini s/d hari ini
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        // Base Query (Hanya status paid)
        $baseQuery = Sale::where('status', 'paid')
            ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // 1. Ringkasan
        $summary = $baseQuery->clone()
            ->select(
                DB::raw('SUM(total) as total_omzet'),
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('AVG(total) as rata_rata'),
                DB::raw('SUM(discount) as total_diskon')
            )->first();

        // 2. Penjualan Per Hari
        $dailySales = $baseQuery->clone()
            ->select(
                DB::raw('DATE(sale_date) as tanggal'),
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(total) as omzet')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->get();

        // 3. Produk Terlaris (10 besar)
        $topProducts = SaleItem::whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->where('status', 'paid')
                  ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->select(
                'product_name',
                DB::raw('SUM(quantity) as total_terjual'),
                DB::raw('SUM(subtotal) as total_omzet')
            )
            ->groupBy('product_name', 'product_id')
            ->orderByDesc('total_terjual')
            ->take(10)
            ->get();

        // 4. Laba Kotor (Omzet - Modal)
        $profit = SaleItem::whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->where('status', 'paid')
                  ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->select(DB::raw('SUM((price - purchase_price) * quantity) as total_laba'))
            ->first();

        // 5. Stok Menipis
        $lowStock = Product::whereColumn('stock', '<=', 'min_stock')->get();

        return view('reports.index', compact(
            'startDate', 'endDate', 'summary', 'dailySales', 'topProducts', 'profit', 'lowStock'
        ));
    }

    // ==========================================
    // METHOD BARU UNTUK EXPORT EXCEL (BONUS B1)
    // ==========================================
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        // Nama file Excel
        $fileName = 'Laporan_Penjualan_' . $startDate . '_sampai_' . $endDate . '.xlsx';

        // Proses download menggunakan class SalesExport
        return Excel::download(new SalesExport($startDate, $endDate), $fileName);
    }
}