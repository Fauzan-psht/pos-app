@extends('layouts.app')

@section('content')
    <h4 class="mb-4 fw-bold">Ringkasan Hari Ini</h4>
    
    <!-- Kartu Ringkasan -->
    <div class="row g-3 mb-5">
        <!-- Penjualan Hari Ini -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0d6efd, #0dcaf0); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 opacity-75">Penjualan Hari Ini</h6>
                        <i class="bi bi-cash-stack fs-3 opacity-50"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Transaksi Hari Ini -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #198754, #20c997); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 opacity-75">Transaksi Hari Ini</h6>
                        <i class="bi bi-receipt fs-3 opacity-50"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">{{ $todayTransactions }}</h3>
                </div>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffc107, #fd7e14); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 opacity-75">Stok Menipis</h6>
                        <i class="bi bi-exclamation-triangle fs-3 opacity-50"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">{{ $lowStockProducts }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Produk Aktif -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0dcaf0, #0d6efd); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 opacity-75">Produk Aktif</h6>
                        <i class="bi bi-box-seam fs-3 opacity-50"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">{{ $activeProducts }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel 5 Transaksi Terakhir -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i> 5 Transaksi Terakhir</h6>
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No Invoice</th>
                            <th>Waktu</th>
                            <th>Pelanggan</th>
                            <th>Kasir</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $sale->invoice_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td>
                                <td>{{ $sale->customer ? $sale->customer->name : 'Umum' }}</td>
                                <td>{{ $sale->user->name }}</td>
                                <td class="fw-bold text-primary">@rupiah($sale->total)</td>
                                <td>
                                    @if($sale->status === 'paid')
                                        <span class="badge bg-success">Sukses</span>
                                    @else
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection