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
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
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

                <div class="mb-4">
                    <label for="store-logo" class="form-label fw-bold">Logo Toko untuk Struk</label>
                    <input type="hidden" name="image_type" value="{{ \App\Enums\ImageType::StoreLogo->value }}">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3 text-center">
                            <img
                                id="store-logo-preview"
                                src="{{ $storeLogoUrl ?? '' }}"
                                alt="Pratinjau logo toko"
                                class="img-thumbnail {{ $storeLogoUrl ? '' : 'd-none' }}"
                                style="max-width: 180px; max-height: 90px; object-fit: contain;"
                            >
                            <div id="store-logo-placeholder" class="p-4 bg-light rounded border text-muted {{ $storeLogoUrl ? 'd-none' : '' }}">
                                Belum ada logo
                            </div>
                        </div>
                        <div class="col-md-9">
                            <input
                                type="file"
                                id="store-logo"
                                name="store_logo"
                                class="form-control @error('store_logo') is-invalid @enderror"
                                accept="image/jpeg,image/png,image/webp"
                            >
                            <small class="form-text text-muted">PNG, JPG, atau WebP, maksimal 5 MB. Untuk hasil cetak yang jelas, gunakan logo kontras tinggi.</small>
                            @error('store_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

@push('scripts')
    <script>
        document.getElementById('store-logo').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }

            const preview = document.getElementById('store-logo-preview');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
            document.getElementById('store-logo-placeholder').classList.add('d-none');
        });
    </script>
@endpush