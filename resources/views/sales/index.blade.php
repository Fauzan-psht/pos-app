@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Riwayat Transaksi</h4>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label>Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label>No Invoice</label>
                    <input type="text" name="invoice" class="form-control" placeholder="Cari No Invoice..." value="{{ request('invoice') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>No Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->customer ? $sale->customer->name : 'Umum' }}</td>
                            <td>{{ $sale->user->name }}</td>
                            <td class="text-end">@rupiah($sale->total)</td>
                            <td><span class="badge bg-secondary">{{ strtoupper($sale->payment_method) }}</span></td>
                            <td>
                                @if($sale->status === 'paid')
                                    <span class="badge bg-success">Lunas</span>
                                @else
                                    <span class="badge bg-danger">Void</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-info text-white">Struk</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $sales->links() }}
        </div>
    </div>
@endsection