<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - POS App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* KUNCI DIBUKA: Halaman sekarang bisa discroll ke bawah */
        body { 
            background-color: #f4f6f9; 
            min-height: 100vh; 
            overflow-y: auto; /* Bisa discroll */
            display: flex; 
            flex-direction: column; 
            transition: background-color 0.3s; 
        }
        
        .main-container { flex-grow: 1; padding: 15px; }
        .product-list { height: calc(100vh - 160px); overflow-y: auto; padding-right: 5px; }
        .cart-card { height: 100%; display: flex; flex-direction: column; }
        .cart-list { flex-grow: 1; overflow-y: auto; }
        .product-card { cursor: pointer; transition: 0.2s; }
        .product-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 5px; }

        #loading-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center;
            align-items: center; flex-direction: column;
        }
        .spinner-box {
            padding: 30px; border-radius: 15px; background: #fff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center;
        }

        /* === PERBAIKAN TAMPILAN DARK MODE UNTUK KASIR === */
        [data-bs-theme="dark"] body { background-color: #121212 !important; color: #e0e0e0; }
        [data-bs-theme="dark"] .navbar { background-color: #1e1e2d !important; }
        [data-bs-theme="dark"] .card, [data-bs-theme="dark"] .bg-white, [data-bs-theme="dark"] .card-footer {
            background-color: #1e1e2d !important; color: #e0e0e0; border-color: #2d2d3f !important;
        }
        [data-bs-theme="dark"] .product-card { background-color: #1e1e2d !important; }
        [data-bs-theme="dark"] .bg-light { background-color: #2d2d3f !important; color: #e0e0e0; }
        [data-bs-theme="dark"] .text-dark { color: #e0e0e0 !important; }
        [data-bs-theme="dark"] .text-muted { color: #a2a3b7 !important; }
        [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .form-select {
            background-color: #2d2d3f; color: #fff; border-color: #3f3f5a;
        }
    </style>
</head>
<body>

    <div id="loading-overlay">
        <div class="spinner-box bg-body">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
            <h5 class="fw-bold mb-1">Memproses Transaksi...</h5>
            <p class="text-muted mb-0" style="font-size: 14px;">Mohon tunggu sebentar</p>
        </div>
    </div>

    <nav class="navbar navbar-dark bg-dark px-3 flex-shrink-0">
        <span class="navbar-brand mb-0 h1"><i class="bi bi-shop"></i> Kasir POS</span>
        <div class="d-flex align-items-center text-white">
            <button id="theme-toggle-pos" class="btn btn-sm btn-outline-light me-3" type="button" title="Ganti Tema">
                <i class="bi bi-moon-fill" id="theme-icon-pos"></i>
            </button>
            
            <span class="me-3">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light me-2">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-danger">Logout</button>
            </form>
        </div>
    </nav>

    <div class="main-container container-fluid">
        @if(session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dark Mode
            const themeToggleBtn = document.getElementById('theme-toggle-pos');
            const themeIcon = document.getElementById('theme-icon-pos');
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

            @if(session('success'))
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", showConfirmButton: false, timer: 2000 });
            @endif
        });
    </script>
    @stack('scripts')
    @include('partials.form-submit-feedback')
</body>
</html>