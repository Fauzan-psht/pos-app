<div class="bg-dark text-white p-3 d-flex flex-column" style="width: 250px; min-height: 100vh;">
    <h5 class="mb-4 text-center">POS App</h5>
    <ul class="nav flex-column mb-auto">
        
        {{-- Menu Dashboard (Semua Role) --}}
        <li class="nav-item mb-2">
            <a href="{{ route('dashboard') }}" class="nav-link text-white {{ request()->routeIs('dashboard') ? 'bg-secondary rounded' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        
        {{-- MENU KHUSUS ADMIN --}}
        @if(auth()->user()->role === 'admin')
            <li class="nav-item mb-2">
                <a href="{{ route('categories.index') }}" class="nav-link text-white {{ request()->routeIs('categories.*') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-tags me-2"></i> Kategori
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a href="{{ route('products.index') }}" class="nav-link text-white {{ request()->routeIs('products.*') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-box me-2"></i> Produk
                </a>
            </li>
            
            <li class="nav-item mb-2">
                <a href="{{ route('customers.index') }}" class="nav-link text-white {{ request()->routeIs('customers.*') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-people me-2"></i> Pelanggan
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="{{ route('suppliers.index') }}" class="nav-link text-white {{ request()->routeIs('suppliers.*') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-truck me-2"></i> Supplier
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="{{ route('purchases.index') }}" class="nav-link text-white {{ request()->routeIs('purchases.*') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-cart-plus me-2"></i> Stok Masuk
                </a>
            </li>

            {{-- Menu Kasir (Admin) --}}
            <li class="nav-item mb-2">
                <a href="{{ route('sales.create') }}" class="nav-link text-white {{ request()->routeIs('sales.create') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-cart me-2"></i> Kasir
                </a>
            </li>

            {{-- Menu Riwayat (Admin) --}}
            <li class="nav-item mb-2">
                <a href="{{ route('sales.index') }}" class="nav-link text-white {{ request()->routeIs('sales.index') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-clock-history me-2"></i> Riwayat
                </a>
            </li>

            {{-- Menu Laporan (Sudah diupdate) --}}
            <li class="nav-item mb-2">
                <a href="{{ route('reports.index') }}" class="nav-link text-white {{ request()->routeIs('reports.*') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan
                </a>
            </li>

            {{-- Menu Users (Sudah diupdate) --}}
            <li class="nav-item mb-2">
                <a href="{{ route('users.index') }}" class="nav-link text-white {{ request()->routeIs('users.*') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-person-gear me-2"></i> Users
                </a>
            </li>

            {{-- Menu Pengaturan (Sudah diupdate) --}}
            <li class="nav-item mb-2">
                <a href="{{ route('settings.index') }}" class="nav-link text-white {{ request()->routeIs('settings.*') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-gear me-2"></i> Pengaturan
                </a>
            </li>
        @endif

        {{-- MENU KHUSUS KASIR --}}
        @if(auth()->user()->role === 'kasir')
            <li class="nav-item mb-2">
                <a href="{{ route('customers.index') }}" class="nav-link text-white {{ request()->routeIs('customers.*') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-people me-2"></i> Pelanggan
                </a>
            </li>
            
            {{-- Menu Kasir (Kasir) --}}
            <li class="nav-item mb-2">
                <a href="{{ route('sales.create') }}" class="nav-link text-white {{ request()->routeIs('sales.create') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-cart me-2"></i> Kasir
                </a>
            </li>

            {{-- Menu Riwayat (Kasir) --}}
            <li class="nav-item mb-2">
                <a href="{{ route('sales.index') }}" class="nav-link text-white {{ request()->routeIs('sales.index') ? 'bg-secondary rounded' : '' }}">
                    <i class="bi bi-clock-history me-2"></i> Riwayat
                </a>
            </li>
        @endif

    </ul>
</div>