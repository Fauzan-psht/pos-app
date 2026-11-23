<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS App - @yield('title', 'Dashboard')</title>
    
    @php
        $storeLogo = \App\Models\Setting::get('store_logo');
        $storeName = \App\Models\Setting::get('store_name', 'POS App');
    @endphp
    
    @if($storeLogo && file_exists(public_path('images/settings/' . $storeLogo)))
        <link rel="icon" type="image/png" href="{{ asset('images/settings/' . $storeLogo) }}">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- JQUERY & DATATABLES (WAJIB BUAT SERVER-SIDE) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $.extend(true, $.fn.dataTable.defaults, {
            dom: "<'row mb-2'<'col-12'f>>" +
                "<'row'<'col-12'tr>>" +
                "<'row mt-2 align-items-center'<'col-12 col-md-6'i><'col-12 col-md-6'p>>"
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #loading-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center;
            align-items: center; flex-direction: column;
        }
        .spinner-box {
            padding: 30px; border-radius: 15px; background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center;
        }
        [data-bs-theme="dark"] body { background-color: #121212 !important; color: #e0e0e0; }
        [data-bs-theme="dark"] .bg-white { background-color: #1e1e2d !important; }
        [data-bs-theme="dark"] .navbar { background-color: #1e1e2d !important; border-bottom: 1px solid #2d2d3f !important; }
        [data-bs-theme="dark"] .navbar-text { color: #e0e0e0 !important; }
        [data-bs-theme="dark"] .card { background-color: #1e1e2d !important; color: #e0e0e0; border-color: #2d2d3f; }
        [data-bs-theme="dark"] .table { --bs-table-bg: #1e1e2d; --bs-table-color: #e0e0e0; --bs-table-hover-bg: #2d2d3f; --bs-table-border-color: #2d2d3f; }
        [data-bs-theme="dark"] .table-light th { background-color: #2d2d3f !important; color: #ffffff; }
        [data-bs-theme="dark"] .text-dark { color: #e0e0e0 !important; }
        [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .form-select { background-color: #2d2d3f; color: #fff; border-color: #3f3f5a; }
        [data-bs-theme="dark"] .dropdown-menu { background-color: #1e1e2d; border-color: #2d2d3f; }
        [data-bs-theme="dark"] .dropdown-item { color: #e0e0e0; }
        [data-bs-theme="dark"] .dropdown-item:hover { background-color: #2d2d3f; }
        
        /* Style tambahan biar DataTables nyatu sama Dark Mode */
        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_length,
        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_filter,
        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_info,
        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_processing,
        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate {
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_filter input,
        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_length select {
            background-color: #2d2d3f;
            color: #fff;
            border: 1px solid #3f3f5a;
        }
        table.dataTable thead > tr > th[class*="sorting"]::before,
        table.dataTable thead > tr > th[class*="sorting"]::after {
            display: none !important;
        }
        .dataTables_wrapper .dataTables_filter {
            width: 100%;
            float: none !important;
            margin-bottom: 0.75rem;
            text-align: left !important;
        }
        .dataTables_wrapper .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            max-width: 24rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            flex: 1;
            min-width: 0;
            margin-left: 0 !important;
        }
    </style>
</head>
<body class="bg-light">

    <div id="loading-overlay">
        <div class="spinner-box bg-body">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
            <h5 class="fw-bold mb-1">Mohon Tunggu...</h5>
            <p class="text-muted mb-0" style="font-size: 14px;">Sedang memproses data</p>
        </div>
    </div>

    <div class="d-flex">
        @include('partials.sidebar')

        <div class="flex-grow-1 d-flex flex-column" style="min-height: 100vh; width: calc(100% - 250px);">
            @include('partials.navbar')

            <div class="p-4 flex-grow-1">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- MODAL PROFIL USER -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-gear me-2"></i> Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                    <!-- FORM 1: UPLOAD FOTO (TERPISAH) -->
                    <div class="text-center mb-4 p-3 border rounded">
                        @if(auth()->user()->photo && file_exists(public_path('images/profiles/' . auth()->user()->photo)))
                            <img src="{{ asset('images/profiles/' . auth()->user()->photo) }}?t={{ time() }}" class="rounded-circle shadow-sm mb-3" style="width: 100px; height: 100px; object-fit: cover;" id="preview-photo">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D6EFD&color=fff&size=100" class="rounded-circle shadow-sm mb-3" style="width: 100px; height: 100px; object-fit: cover;" id="preview-photo">
                        @endif

                        <form action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="photo" class="form-control" accept="image/*" required>
                                <button type="submit" class="btn btn-primary">Upload Foto</button>
                            </div>
                            <small class="text-muted d-block mt-1">Format: JPG, PNG, WEBP. Maks 5MB.</small>
                        </form>
                    </div>

                    <!-- FORM 2: DATA DIRI (TERPISAH) -->
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                        </div>
                        
                        @if(auth()->user()->role === 'admin')
                            <hr>
                            <p class="text-muted small mb-2">Ubah Password (Opsional)</p>
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        @else
                            <hr>
                            <div class="alert alert-info py-2 mb-0" style="font-size: 13px;">
                                <i class="bi bi-info-circle me-1"></i> Untuk mengubah password, silakan hubungi Admin Toko.
                            </div>
                        @endif
                        
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL SETTING TOKO (ADMIN) -->
    @if(auth()->user()->role === 'admin')
    <div class="modal fade" id="storeSettingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-shop me-2"></i> Pengaturan Toko</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('store.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            @if($storeLogo && file_exists(public_path('images/settings/' . $storeLogo)))
                                <img src="{{ asset('images/settings/' . $storeLogo) }}" class="img-thumbnail shadow-sm" style="max-height: 100px; object-fit: contain;" id="preview-logo">
                            @else
                                <div class="p-4 bg-light rounded border text-muted" id="logo-placeholder">Belum ada logo</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Logo Toko</label>
                            <input type="file" name="store_logo" class="form-control" accept="image/*" onchange="previewLogo(event)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Toko</label>
                            <input type="text" name="store_name" class="form-control" value="{{ $storeName }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview-photo');
                output.src = reader.result;
                output.style.width = '100px';
                output.style.height = '100px';
                output.style.objectFit = 'cover';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function previewLogo(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview-logo');
                if(output) {
                    output.src = reader.result;
                } else {
                    const container = event.target.closest('.modal-body').querySelector('.text-center');
                    container.innerHTML = `<img src="${reader.result}" class="img-thumbnail shadow-sm" style="max-height: 100px; object-fit: contain;" id="preview-logo">`;
                }
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const loadingOverlay = document.getElementById('loading-overlay');

            // --- 1. FITUR DARK MODE ---
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const currentTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', currentTheme);
            
            if (currentTheme === 'dark') { themeIcon.classList.replace('bi-moon-fill', 'bi-sun-fill'); }

            if(themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    let theme = document.documentElement.getAttribute('data-bs-theme');
                    if (theme === 'light') {
                        document.documentElement.setAttribute('data-bs-theme', 'dark');
                        localStorage.setItem('theme', 'dark');
                        themeIcon.classList.replace('bi-moon-fill', 'bi-sun-fill');
                    } else {
                        document.documentElement.setAttribute('data-bs-theme', 'light');
                        localStorage.setItem('theme', 'light');
                        themeIcon.classList.replace('bi-sun-fill', 'bi-moon-fill');
                    }
                });
            }

            // --- 2. MATIKAN NOTIFIKASI KUNO BAWAAN BROWSER ---
            document.querySelectorAll('form').forEach(form => {
                form.removeAttribute('onsubmit');
            });

            // --- 3. ANIMASI HAPUS & LOADING SIMPAN + DISABLE BUTTON ---
            document.querySelectorAll('form').forEach(form => {
                const isDeleteForm = form.querySelector('input[name="_method"][value="DELETE"]');
                
                if (isDeleteForm) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault(); 
                        Swal.fire({
                            title: 'Yakin ingin menghapus?',
                            text: "Data yang dihapus tidak dapat dikembalikan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="bi bi-trash"></i> Ya, Hapus!',
                            cancelButtonText: 'Batal',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                loadingOverlay.style.display = 'flex';
                                HTMLFormElement.prototype.submit.call(form);
                            }
                        });
                    });
                } else {
                    form.addEventListener('submit', function(e) {
                        if (!form.checkValidity()) return; 
                        
                        const submitBtn = form.querySelector('button[type="submit"]');
                        
                        if (submitBtn) { 
                            // DISABLE BUTTON & GANTI TEKS
                            submitBtn.disabled = true; 
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
                        }
                        
                        loadingOverlay.style.display = 'flex'; 
                    });
                }
            });

            // --- 4. VALIDASI ANGKA & NO HP ---
            const numericInputs = document.querySelectorAll('input[type="number"], input[name*="phone"], input[name*="harga"], input[name*="price"], input[name*="stock"], input[name*="quantity"], input[name*="discount"]');
            numericInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9\-\.]/g, '');
                });
            });

            const phoneInputs = document.querySelectorAll('input[name*="phone"]');
            phoneInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13);
                });
            });

            // --- 5. OTOMATISASI BINTANG & HAPUS TEKS (OPSIONAL) ---
            const requiredInputs = document.querySelectorAll('input[required], select[required], textarea[required]');
            requiredInputs.forEach(input => {
                let label = document.querySelector(`label[for="${input.id}"]`);
                if (!label) label = input.closest('.mb-3, .mb-2')?.querySelector('label');
                if (label && !label.innerHTML.includes('*')) {
                    label.innerHTML += ' <span class="text-danger">*</span>';
                }
            });

            const labels = document.querySelectorAll('label');
            labels.forEach(label => {
                if (label.innerHTML.toLowerCase().includes('(opsional)')) {
                    label.innerHTML = label.innerHTML.replace(/\(opsional\)/gi, '').trim();
                    if (!label.innerHTML.includes('*')) label.innerHTML += ' <span class="text-danger">*</span>';
                    
                    let inputId = label.getAttribute('for');
                    let input = document.getElementById(inputId) || label.nextElementSibling;
                    if (input) input.setAttribute('required', 'required');
                }
            });

            // --- 6. SWEETALERT SUCCESS / ERROR ---
            @if(session('success'))
                Swal.fire({
                    icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}",
                    showConfirmButton: false, timer: 2000, timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", confirmButtonColor: '#d33',
                });
            @endif
        });
    </script>
    @stack('scripts')
</body>
</html>