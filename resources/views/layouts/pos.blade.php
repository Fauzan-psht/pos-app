<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - POS App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }
        /* Menghilangkan batasan tinggi layar agar bisa di-scroll */
        .main-container { padding: 15px; min-height: calc(100vh - 56px); }
        .product-card { cursor: pointer; transition: 0.2s; }
        .product-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 5px; }
    </style>
</head>
<body>

    <!-- Navbar Atas -->
    <nav class="navbar navbar-dark bg-dark px-3">
        <span class="navbar-brand mb-0 h1"><i class="bi bi-shop"></i> Kasir POS</span>
        <div class="d-flex align-items-center text-white">
            <span class="me-3">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light me-2">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-danger">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Kontainer Utama -->
    <div class="main-container container-fluid">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>