<div class="bg-dark text-white p-3 d-flex flex-column" style="width: 250px; min-height: 100vh;">
    <h5 class="mb-4 text-center">POS App</h5>
    <ul class="nav flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="{{ route('dashboard') }}" class="nav-link text-white {{ request()->routeIs('dashboard') ? 'bg-secondary rounded' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        
        @if(auth()->user()->role === 'admin')
            <li class="nav-item mb-2">
                <a href="{{ route('categories.index') }}" class="nav-link text-white {{ request()->routeIs('categories.*') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-tags me-2"></i> Kategori
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-box me-2"></i> Produk
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-people me-2"></i> Pelanggan
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-truck me-2"></i> Supplier
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-cart-plus me-2"></i> Stok Masuk
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-person-gear me-2"></i> Users
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-gear me-2"></i> Pengaturan
                </a>
            </li>
        @else
            <!-- Menu Khusus Kasir -->
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-cart me-2"></i> Kasir
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-clock-history me-2"></i> Riwayat
                </a>
            </li>
        @endif
    </ul>
</div>