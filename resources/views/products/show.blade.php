@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Detail Produk</h4>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid img-thumbnail">
                    @else
                        <div class="bg-light d-flex justify-content-center align-items-center" style="height: 200px;">No Image</div>
                    @endif
                </div>
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr><th width="200">SKU</th><td>{{ $product->sku }}</td></tr>
                        <tr><th>Nama Produk</th><td>{{ $product->name }}</td></tr>
                        <tr><th>Kategori</th><td>{{ $product->category->name }}</td></tr>
                        <tr><th>Harga Modal</th><td>@rupiah($product->purchase_price)</td></tr>
                        <tr><th>Harga Jual</th><td>@rupiah($product->selling_price)</td></tr>
                        <tr><th>Sisa Stok</th><td>{{ $product->stock }} {{ $product->unit }}</td></tr>
                        <tr><th>Minimal Stok</th><td>{{ $product->min_stock }}</td></tr>
                        <tr><th>Status</th><td>{!! $product->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' !!}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection