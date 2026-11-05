@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Detail Stok Masuk: {{ $purchase->purchase_number }}</h4>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Supplier:</strong> {{ $purchase->supplier->name }}</p>
                    <p><strong>Tanggal Masuk:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d F Y') }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <p><strong>Dicatat Oleh:</strong> {{ $purchase->user->name }}</p>
                    <p><strong>Catatan:</strong> {{ $purchase->note ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                        <tr>
                            <td>{{ $item->product->name }} ({{ $item->product->sku }})</td>
                            <td class="text-end">@rupiah($item->purchase_price)</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">@rupiah($item->subtotal)</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total Belanja:</th>
                        <th class="text-end">@rupiah($purchase->total_amount)</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection