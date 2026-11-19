<style>
    .sidebar-custom {
        background: #1e1e2d; 
        border-right: 1px solid #2d2d3f;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        width: 250px;
        min-height: 100vh;
        padding-bottom: 20px;
    }
    .sidebar-brand {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 22px 15px;
        margin-bottom: 10px;
        border-bottom: 1px solid #2d2d3f;
        text-decoration: none;
    }
    .logo-box {
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        overflow: hidden;
    }
    .logo-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .sidebar-brand span {
        font-size: 15px;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #ffffff;
        line-height: 1.1;
        white-space: normal; 
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-width: 160px;
    }
    .menu-category {
        font-size: 11px;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 15px 20px 5px 20px;
        margin-top: 10px;
    }
    .sidebar-custom .nav-link {
        color: #a2a3b7 !important;
        font-size: 14px;
        font-weight: 500;
        padding: 10px 20px;
        margin: 2px 15px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .sidebar-custom .nav-link:hover {
        color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.05);
        transform: translateX(5px);
    }
    .sidebar-custom .nav-link.active-menu {
        background: linear-gradient(90deg, #6f42c1 0%, #0d6efd 100%);
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
    
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="logo-box">
            @php
                $sidebarLogo = \App\Models\Setting::get('store_logo');
            @endphp
            
            @if($sidebarLogo && file_exists(public_path('images/settings/' . $sidebarLogo)))
                <img src="{{ asset('images/settings/' . $sidebarLogo) }}" alt="Logo Toko">
            @else
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 24px; height: 24px; fill: #ffffff;">
                    <path d="M2 5h20v3H2V5zm2 5h16v11H4V10zm2 2v7h4v-7H6zm6 0v7h4v-7h-4z"/>
                    <circle cx="9" cy="4" r="1"/>
                    <circle cx="15" cy="4" r="1"/>
                    <path d="M10 2h4l1 1h-6l1-1z"/>
                </svg>
            @endif
        </div>
        <span>{{ \App\Models\Setting::get('store_name', 'POS APP') }}</span>
    </a>

    <ul class="nav flex-column mb-auto">
        
        <div class="menu-category">Utama</div>
        
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active-menu' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('sales.create') }}" class="nav-link {{ request()->routeIs('sales.create') ? 'active-menu' : '' }}">
                <i class="bi bi-cart-fill"></i> Kasir
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.index') ? 'active-menu' : '' }}">
                <i class="bi bi-clock-history"></i> Riwayat Transaksi
            </a>
        </li>

        @if(auth()->user()->role === 'admin')
        
            <div class="menu-category">Master Data</div>
            
            <li class="nav-item">
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-box-seam-fill"></i> Produk
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-tags-fill"></i> Kategori
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

            <div class="menu-category">Laporan & Setting</div>
            
            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-person-gear"></i> Manajemen User
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-gear-fill"></i> Pengaturan
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('logs.index') }}" class="nav-link {{ request()->routeIs('logs.*') ? 'active-menu' : '' }}">
                    <i class="bi bi-list-check"></i> Log Aktivitas
                </a>
            </li>
            
        @endif

    </ul>
</div>