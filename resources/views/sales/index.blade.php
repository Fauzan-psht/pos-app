@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Riwayat Transaksi</h4>
        <span class="text-muted">{{ auth()->user()->name }} - {{ ucfirst(auth()->user()->role) }}</span>
    </div>

    {{-- Card Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Dari Tanggal">
                </div>
                <div class="col-md-3">
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="Sampai Tanggal">
                </div>
                <div class="col-md-4">
                    <input type="text" name="invoice" class="form-control" placeholder="Cari nomor invoice..." value="{{ request('invoice') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td class="ps-4"><a href="{{ route('sales.show', $sale->id) }}" class="fw-bold text-decoration-none">{{ $sale->invoice_number }}</a></td>
                            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->user->name }}</td>
                            <td>{{ $sale->customer ? $sale->customer->name : 'Umum' }}</td>
                            <td class="text-end fw-bold">@rupiah($sale->total)</td>
                            <td>
                                {{-- Status Badge --}}
                                @if($sale->status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success">Sukses</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{-- Ikon Mata mengarah ke Detail --}}
                                <a href="{{ route('sales.detail', $sale->id) }}" class="btn btn-sm btn-outline-secondary me-1">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                
                                {{-- Ikon Printer mengarah ke Struk --}}
                                <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Menampilkan {{ $sales->firstItem() ?? 0 }}-{{ $sales->lastItem() ?? 0 }} dari {{ $sales->total() }} transaksi</span>
                {{ $sales->links() }}
            </div>
        </div>
    </div>
@endsection