@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Riwayat Stok Masuk</h4>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">Tambah Stok Masuk</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No. Dokumen</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Total Belanja</th>
                        <th>Dicatat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->purchase_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                            <td>{{ $purchase->supplier->name }}</td>
                            <td class="text-end">@rupiah($purchase->total_amount)</td>
                            <td>{{ $purchase->user->name }}</td>
                            <td>
                                <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-info text-white">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Belum ada data stok masuk.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $purchases->links() }}
        </div>
    </div>
@endsection