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

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-person-circle me-2"></i>Foto Profil Admin</h5>
        </div>
        <div class="card-body p-4">
            <form id="profile-photo-form" action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="image_type" value="{{ \App\Enums\ImageType::Profile->value }}">

                <div class="row g-3 align-items-center">
                    <div class="col-md-3 text-center">
                        <img
                            id="admin-photo-preview"
                            src="{{ $profilePhotoUrl ?? '' }}"
                            alt="Pratinjau foto profil admin"
                            class="rounded-circle shadow-sm {{ $profilePhotoUrl ? '' : 'd-none' }}"
                            style="width: 110px; height: 110px; object-fit: cover;"
                        >
                        <div id="admin-photo-placeholder" class="p-4 bg-light rounded border text-muted {{ $profilePhotoUrl ? 'd-none' : '' }}">
                            Belum ada foto admin
                        </div>
                    </div>
                    <div class="col-md-9">
                        <label for="admin-photo" class="form-label fw-bold">Pilih Foto Admin</label>
                        <input
                            type="file"
                            id="admin-photo"
                            name="photo"
                            class="form-control @error('photo') is-invalid @enderror"
                            accept="image/jpeg,image/png,image/webp"
                            required
                        >
                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="form-text text-muted d-block mb-3">JPG, PNG, atau WebP, maksimal 5 MB.</small>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-1"></i> Simpan Foto Admin
                        </button>
                    </div>
                </div>
            </form>
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
                    <label for="store-logo" class="form-label fw-bold">Logo Toko</label>
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
                            <small class="form-text text-muted">Logo ini tampil di halaman login, sidebar, dan struk. PNG, JPG, atau WebP, maksimal 5 MB.</small>
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
        document.getElementById('admin-photo').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }

            const preview = document.getElementById('admin-photo-preview');
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
            document.getElementById('admin-photo-placeholder').classList.add('d-none');
        });

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