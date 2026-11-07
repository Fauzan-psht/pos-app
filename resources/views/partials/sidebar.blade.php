<style>
    /* Desain Custom Sidebar */
    .sidebar-custom {
        background: #1e1e2d; 
        border-right: 1px solid #2d2d3f;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        width: 250px;
        min-height: 100vh;
    }
    
    /* Logo POS App */
    .sidebar-brand {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 22px 15px; /* Padding horizontal ditambah agar teks tidak mepet */
        margin-bottom: 10px;
        border-bottom: 1px solid #2d2d3f;
        text-decoration: none;
    }
    
    /* Kotak Ikon Logo Baru */
    .logo-box {
        width: 38px;
        height: 38px;
        flex-shrink: 0; /* Mencegah kotak mengecil jika teks panjang */
        background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }
    
    .logo-box i {
        font-size: 20px;
        color: #ffffff;
        margin: 0;
    }

    /* Teks Nama Toko */
    .sidebar-brand span {
        font-size: 18px; /* Sedikit dikecilkan dari 20px agar muat teks panjang */
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #ffffff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis; /* Jika nama toko sangat panjang, akan muncul titik-titik (...) */
        max-width: 160px;
    }

    /* Gaya Menu */
    .sidebar-custom .nav-link {
        color: #a2a3b7 !important;
        font-size: 14px;
        font-weight: 500;
        padding: 12px 20px;
        margin: 4px 15px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    /* Efek saat kursor diarahkan */
    .sidebar-custom .nav-link:hover {
        color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.05);
        transform: translateX(5px);
    }

    /* Efek Menu Aktif */
    .sidebar-custom .nav-link.active-menu {
        background: linear-gradient(90deg, #0d6efd 0%, #0b5ed7 100%);
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
    }

    .sidebar-custom .nav-link i {
        font-size: 18px;
        margin-right: 12px;
        vertical-align: middle;
    }
</style>

<div class="sidebar-custom text-white d-flex flex-column">
    
    <!-- Logo / Brand (Dinamis dari Pengaturan) -->
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="logo-box">
            <i class="bi bi-shop-window"></i>
        </div>
        <span>{{ \App\Models\Setting::get('store_name', 'POS APP') }}</span>
    </a>

    <ul class="nav flex-column mb-auto pt-2">
        
        {{-- Menu Dashboard (Semua Role) --}}
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active-menu' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </li>
        
        {{-- MENU KHUSUS ADMIN --}}
        @if(auth()->user()->role === 'admin')
            <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-tags-fill"></i> Kategori
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-box-seam-fill"></i> Produk
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-people-fill"></i> Pelanggan
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-truck"></i> Supplier
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-cart-plus-fill"></i> Stok Masuk
                </a>
            </li>

            {{-- Menu Kasir (Admin) --}}
            <li class="nav-item">
                <a href="{{ route('sales.create') }}" class="nav-link {{ request()->routeIs('sales.create') ? 'active-menu' : '' }}">
                    <i class="bi bi-cart-fill"></i> Kasir
                </a>
            </li>

            {{-- Menu Riwayat (Admin) --}}
            <li class="nav-item">
                <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.index') ? 'active-menu' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat
                </a>
            </li>

            {{-- Menu Laporan --}}
            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
                </a>
            </li>

            {{-- Menu Users --}}
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-person-gear"></i> Users
                </a>
            </li>

            {{-- Menu Pengaturan --}}
            <li class="nav-item">
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-gear-fill"></i> Pengaturan
                </a>
            </li>
        @endif

        {{-- MENU KHUSUS KASIR --}}
        @if(auth()->user()->role === 'kasir')
            <li class="nav-item">
                <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-people-fill"></i> Pelanggan
                </a>
            </li>
            
            {{-- Menu Kasir (Kasir) --}}
            <li class="nav-item">
                <a href="{{ route('sales.create') }}" class="nav-link {{ request()->routeIs('sales.create') ? 'active-menu' : '' }}">
                    <i class="bi bi-cart-fill"></i> Kasir
                </a>
            </li>

            {{-- Menu Riwayat (Kasir) --}}
            <li class="nav-item">
                <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.index') ? 'active-menu' : '' }}">
                    <i class="bi bi-clock-history"></i> Riwayat
                </a>
            </li>
        @endif

    </ul>
</div>