@extends('layouts.app')

@section('content')
    <div class="mb-3">
        <h4>{{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">SKU (Kode Produk)</label>
                        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku ?? '') }}">
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8 mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name ?? '') }}">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Satuan (Pcs, Box, dll)</label>
                        <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $product->unit ?? '') }}">
                        @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Modal</label>
                        <input type="number" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" value="{{ old('purchase_price', $product->purchase_price ?? '') }}">
                        @error('purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" name="selling_price" class="form-control @error('selling_price') is-invalid @enderror" value="{{ old('selling_price', $product->selling_price ?? '') }}">
                        @error('selling_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stok Awal (Hanya saat tambah baru)</label>
                        <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock ?? '') }}" {{ isset($product) ? 'readonly' : '' }}>
                        @if(isset($product))
                            <small class="text-muted">Stok hanya bisa diubah lewat menu Stok Masuk atau Kasir.</small>
                        @endif
                        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Minimal Stok (Peringatan)</label>
                        <input type="number" name="min_stock" class="form-control @error('min_stock') is-invalid @enderror" value="{{ old('min_stock', $product->min_stock ?? 5) }}">
                        @error('min_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gambar Produk (Maks 2MB)</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                        @if(isset($product) && $product->image)
                            <small class="text-muted d-block mt-1">Gambar saat ini: <img src="{{ asset('storage/' . $product->image) }}" width="50" class="img-thumbnail"></small>
                        @endif
                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3 d-flex align-items-center">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Produk Aktif (Tampil di Kasir)</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection