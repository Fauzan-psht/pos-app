@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Data Produk</h4>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah Produk</a>
        @endif
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama / SKU..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" width="50" class="img-thumbnail">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td class="text-end">@rupiah($product->selling_price)</td>
                            <td>
                                {{ $product->stock }} {{ $product->unit }}
                                @if($product->stock <= $product->min_stock)
                                    <span class="badge bg-danger ms-1">Stok Menipis</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info text-white">Lihat</a>
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $products->links() }}
        </div>
    </div>
@endsection