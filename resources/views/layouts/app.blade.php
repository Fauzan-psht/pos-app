<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <div class="d-flex" style="min-height: 100vh;">
        <!-- Sidebar -->
        <div class="bg-dark text-white p-3" style="width: 250px;">
            <h5 class="mb-4">POS App</h5>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link text-white"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <!-- Menu Admin -->
                @if(auth()->user()->role === 'admin')
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white"><i class="bi bi-tags"></i> Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white"><i class="bi bi-box"></i> Produk</a>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Navbar -->
            <nav class="navbar navbar-light bg-white border-bottom px-4">
                <span class="navbar-text">
                    Selamat datang, <strong>{{ auth()->user()->name }}</strong> ({{ ucfirst(auth()->user()->role) }})
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
            </nav>

            <!-- Konten -->
            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>