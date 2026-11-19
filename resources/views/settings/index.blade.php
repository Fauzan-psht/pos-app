@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Pengaturan Toko</h4>
    </div>

    <!-- KARTU INFO (Biar Atasnya Ga Polosan) -->
    <div class="alert alert-primary border-0 shadow-sm mb-4 d-flex align-items-center" role="alert" style="background-color: rgba(13, 110, 253, 0.1);">
        <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
        <div>
            <strong>Informasi:</strong> Data yang diubah di sini akan otomatis diterapkan pada nama toko di Sidebar dan bagian kepala/footer pada cetakan Struk Kasir.
        </div>
    </div>

    <!-- FORM PENGATURAN (Layout 2 Kolom) -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- KOLOM KIRI -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Nama Toko</label>
                            <input type="text" name="store_name" class="form-control form-control-lg @error('store_name') is-invalid @enderror" value="{{ old('store_name', $settings['store_name'] ?? '') }}" required>
                            @error('store_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Alamat Toko</label>
                            <input type="text" name="store_address" class="form-control @error('store_address') is-invalid @enderror" value="{{ old('store_address', $settings['store_address'] ?? '') }}" required>
                            @error('store_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- KOLOM KANAN -->
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label fw-bold">No Telp Toko</label>
                            <input type="text" name="store_phone" class="form-control @error('store_phone') is-invalid @enderror" value="{{ old('store_phone', $settings['store_phone'] ?? '') }}" required>
                            @error('store_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pesan Footer Struk</label>
                            <input type="text" name="receipt_footer" class="form-control @error('receipt_footer') is-invalid @enderror" value="{{ old('receipt_footer', $settings['receipt_footer'] ?? '') }}">
                            <small class="text-muted">Teks ini akan muncul di bagian paling bawah struk.</small>
                            @error('receipt_footer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                        <i class="bi bi-save me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection