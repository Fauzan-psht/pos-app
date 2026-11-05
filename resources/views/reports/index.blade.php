@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Laporan Penjualan</h4>
    </div>

    {{-- Filter Tanggal --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label>Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-2 mt-4">
                    <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Omzet</h6>
                    <h4>@rupiah($summary->total_omzet ?? 0)</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Jumlah Transaksi</h6>
                    <h4>{{ $summary->total_transaksi ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Rata-rata / Transaksi</h6>
                    <h4>@rupiah($summary->rata_rata ?? 0)</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6>Laba Kotor</h6>
                    <h4>@rupiah($profit->total_laba ?? 0)</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Penjualan Per Hari --}}
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">Penjualan Per Hari</div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr><th>Tanggal</th><th>Transaksi</th><th class="text-end">Omzet</th></tr>
                        </thead>
                        <tbody>
                            @forelse($dailySales as $ds)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($ds->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $ds->jumlah_transaksi }}</td>
                                    <td class="text-end">@rupiah($ds->omzet)</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Produk Terlaris --}}
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">10 Produk Terlaris</div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr><th>Produk</th><th>Terjual</th><th class="text-end">Omzet</th></tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $tp)
                                <tr>
                                    <td>{{ $tp->product_name }}</td>
                                    <td>{{ $tp->total_terjual }}</td>
                                    <td class="text-end">@rupiah($tp->total_omzet)</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Stok Menipis --}}
    <div class="card">
        <div class="card-header bg-danger text-white">Peringatan Stok Menipis</div>
        <div class="card-body">
            <table class="table table-sm table-hover">
                <thead>
                    <tr><th>Produk</th><th>Sisa Stok</th><th>Batas Minimal</th></tr>
                </thead>
                <tbody>
                    @forelse($lowStock as $ls)
                        <tr>
                            <td>{{ $ls->name }}</td>
                            <td class="text-danger fw-bold">{{ $ls->stock }}</td>
                            <td>{{ $ls->min_stock }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Semua stok aman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection