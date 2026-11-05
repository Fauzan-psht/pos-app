@extends('layouts.app')

@section('content')
    <div class="mb-3">
        <h4>Pengaturan Toko</h4>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-body">
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama Toko</label>
                    <input type="text" name="store_name" class="form-control @error('store_name') is-invalid @enderror" value="{{ old('store_name', $settings['store_name'] ?? '') }}">
                    @error('store_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat Toko</label>
                    <input type="text" name="store_address" class="form-control @error('store_address') is-invalid @enderror" value="{{ old('store_address', $settings['store_address'] ?? '') }}">
                    @error('store_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">No Telp Toko</label>
                    <input type="text" name="store_phone" class="form-control @error('store_phone') is-invalid @enderror" value="{{ old('store_phone', $settings['store_phone'] ?? '') }}">
                    @error('store_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Pesan Footer Struk</label>
                    <input type="text" name="receipt_footer" class="form-control @error('receipt_footer') is-invalid @enderror" value="{{ old('receipt_footer', $settings['receipt_footer'] ?? '') }}">
                    @error('receipt_footer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
            </form>
        </div>
    </div>
@endsection
