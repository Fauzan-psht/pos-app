@extends('layouts.app')

@section('content')
    <h4 class="mb-4">Ringkasan Hari Ini</h4>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card bg-primary text-white mb-3">
                <div class="card-body">
                    <h6>Penjualan Hari Ini</h6>
                    <h3>Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white mb-3">
                <div class="card-body">
                    <h6>Transaksi Hari Ini</h6>
                    <h3>{{ $todayTransactions }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark mb-3">
                <div class="card-body">
                    <h6>Stok Menipis</h6>
                    <h3>{{ $lowStockProducts }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white mb-3">
                <div class="card-body">
                    <h6>Total Produk Aktif</h6>
                    <h3>{{ $activeProducts }}</h3>
                </div>
            </div>
        </div>
    </div>
@endsection