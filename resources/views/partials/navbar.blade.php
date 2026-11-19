<nav class="navbar navbar-expand bg-white shadow-sm px-4 py-2">
    <div class="container-fluid">
        <button class="btn btn-link text-dark d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="bi bi-list fs-4"></i>
        </button>

        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="fw-bold text-primary d-none d-md-block me-3">
                {{ \App\Models\Setting::get('store_name', 'POS App') }}
            </span>

            <!-- LONCENG NOTIFIKASI STOK MENIPIS -->
            @php
                $lowStockCount = \App\Models\Product::whereColumn('stock', '<=', 'min_stock')->where('is_active', true)->count();
                $lowStockProducts = \App\Models\Product::whereColumn('stock', '<=', 'min_stock')->where('is_active', true)->take(5)->get();
            @endphp
            <div class="dropdown">
                <button class="btn btn-light position-relative" data-bs-toggle="dropdown" title="Notifikasi Stok">
                    <i class="bi bi-bell-fill"></i>
                    @if($lowStockCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                            {{ $lowStockCount }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="width: 300px;">
                    <li class="dropdown-header fw-bold">Peringatan Stok Menipis</li>
                    <li><hr class="dropdown-divider"></li>
                    @forelse($lowStockProducts as $prod)
                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('products.index') }}">
                                <span class="text-truncate" style="max-width: 180px;">{{ $prod->name }}</span>
                                <span class="badge bg-danger">{{ $prod->stock }} Tersisa</span>
                            </a>
                        </li>
                    @empty
                        <li><span class="dropdown-item text-muted text-center">Semua stok aman</span></li>
                    @endforelse
                    @if($lowStockCount > 5)
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center text-primary" href="{{ route('products.index') }}">Lihat Semua Produk</a></li>
                    @endif
                </ul>
            </div>

            <button class="btn btn-light" id="theme-toggle" title="Ganti Tema">
                <i class="bi bi-moon-fill" id="theme-icon"></i>
            </button>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                    <!-- FOTO PROFIL: LANGSUNG PAKE ASSET TANPA FILE_EXISTS -->
                    @if(auth()->user()->photo)
                        <img src="{{ asset('images/profiles/' . auth()->user()->photo) }}?t={{ time() }}" class="rounded-circle object-fit-cover me-2" style="width: 35px; height: 35px; border: 2px solid #0d6efd;">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D6EFD&color=fff" class="rounded-circle me-2" style="width: 35px; height: 35px;">
                    @endif
                    <div class="d-none d-md-block text-start">
                        <div class="fw-bold" style="font-size: 14px;">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size: 12px;">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li>
                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal">
                            <i class="bi bi-person me-2"></i> Profil Saya
                        </button>
                    </li>
                    
                    @if(auth()->user()->role === 'admin')
                    <li>
                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#storeSettingModal">
                            <i class="bi bi-shop me-2"></i> Pengaturan Toko
                        </button>
                    </li>
                    @endif

                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>