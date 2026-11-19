@extends('layouts.app')

@section('content')
@php
    $stockValue = old('stock', isset($product)
        ? rtrim(rtrim(number_format((float) $product->stock, 3, '.', ''), '0'), '.')
        : '');
    $minStockValue = old('min_stock', isset($product)
        ? rtrim(rtrim(number_format((float) $product->min_stock, 3, '.', ''), '0'), '.')
        : 5);
    $purchasePriceValue = old('purchase_price', isset($product)
        ? number_format((float) $product->purchase_price, 0, ',', '.')
        : '');
    $sellingPriceValue = old('selling_price', isset($product)
        ? number_format((float) $product->selling_price, 0, ',', '.')
        : '');
@endphp

<style>
    /* Menghilangkan panah scroll pada input angka */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<div class="mb-3">
    <h4>{{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($product))
                @method('PUT')
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">SKU (Kode Produk)</label>
                    <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku ?? '') }}" required>
                    @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-8 mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Satuan (Pcs, Box, dll)</label>
                    <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                        <option value="">-- Pilih Satuan --</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit }}" {{ old('unit', $product->unit ?? '') === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                        @endforeach
                    </select>
                    @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Modal</label>
                    <input type="text" name="purchase_price" id="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" value="{{ $purchasePriceValue }}" inputmode="numeric" required>
                    @error('purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Jual</label>
                    <input type="text" name="selling_price" id="selling_price" class="form-control @error('selling_price') is-invalid @enderror" value="{{ $sellingPriceValue }}" inputmode="numeric" required>
                    @error('selling_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Stok Awal</label>
                    <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ $stockValue }}" min="0" step="0.001" required>
                    @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Minimal Stok</label>
                    <input type="number" name="min_stock" class="form-control @error('min_stock') is-invalid @enderror" value="{{ $minStockValue }}" min="0" step="0.001" required>
                    @error('min_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Gambar Produk</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Produk Aktif</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
    function formatRibuan(angka) {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputs = [document.getElementById('purchase_price'), document.getElementById('selling_price')];
        inputs.forEach(input => {
            if (input) {
                if (input.value) input.value = formatRibuan(input.value);
                input.addEventListener('input', function() {
                    this.value = formatRibuan(this.value);
                });
            }
        });
    });
</script>
@endsection