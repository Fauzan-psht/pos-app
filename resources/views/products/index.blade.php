@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Data Produk</h4>
        @if(auth()->user()->role === 'admin')
            <!-- Tombol ini sekarang memicu Modal, bukan pindah halaman -->
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createProductModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Produk
            </button>
        @endif
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            
            <!-- PENCARIAN & FILTER -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" id="search-product" class="form-control" placeholder="Cari nama / SKU...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="filter-category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ strtolower($cat->name) }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- TABEL PRODUK -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="80" class="text-center">Gambar</th>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga Jual</th>
                            <th class="text-end">Stok</th>
                            <th class="text-end">Stok Minimum</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $stockValue = rtrim(rtrim(number_format((float) $product->stock, 3, '.', ''), '0'), '.');
                                $minStockValue = rtrim(rtrim(number_format((float) $product->min_stock, 3, '.', ''), '0'), '.');
                            @endphp
                            <tr class="product-row" data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku) }}" data-category="{{ strtolower($product->category->name) }}">
                                <td class="text-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="img-thumbnail object-fit-cover" style="width: 60px; height: 60px;">
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $product->sku }}</td>
                                <td class="fw-bold">{{ $product->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td class="text-end">@rupiah($product->selling_price)</td>
                                <td class="text-end">
                                    {{ $stockValue }} {{ $product->unit }}
                                    @if($product->stock <= $product->min_stock)
                                        <span class="badge bg-danger ms-1">Stok Menipis</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ $minStockValue }} {{ $product->unit }}</td>
                                <td class="text-center">
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info text-white" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->role === 'admin')
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- MODAL EDIT PRODUK (BAWAAN LU) -->
                            @if(auth()->user()->role === 'admin')
                            <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Edit Produk: {{ $product->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Kategori</label>
                                                        <select name="category_id" class="form-select" required>
                                                            @foreach($categories as $cat)
                                                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">SKU</label>
                                                        <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" required>
                                                    </div>
                                                    <div class="col-md-8 mb-3">
                                                        <label class="form-label">Nama Produk</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">Satuan</label>
                                                        <select name="unit" class="form-select" required>
                                                            @foreach($units as $unit)
                                                                <option value="{{ $unit }}" {{ $product->unit === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Harga Modal</label>
                                                        <input type="text" name="purchase_price" class="form-control price-input" value="{{ number_format((float) $product->purchase_price, 0, ',', '.') }}" inputmode="numeric" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Harga Jual</label>
                                                        <input type="text" name="selling_price" class="form-control price-input" value="{{ number_format((float) $product->selling_price, 0, ',', '.') }}" inputmode="numeric" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Minimal Stok</label>
                                                        <input type="number" name="min_stock" class="form-control" value="{{ $minStockValue }}" min="0" step="0.001" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Gambar Baru (Opsional)</label>
                                                        <input type="file" name="image" class="form-control">
                                                        @if($product->image)
                                                            <small class="text-muted d-block mt-1">Gambar saat ini: <img src="{{ asset('storage/' . $product->image) }}" width="50" class="img-thumbnail"></small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_{{ $product->id }}" {{ $product->is_active ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_active_{{ $product->id }}">Produk Aktif</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada produk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $products->links() }}</div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL TAMBAH PRODUK (BARU DITAMBAHKAN DI SINI) -->
    <!-- ============================================ -->
    @if(auth()->user()->role === 'admin')
    <div class="modal fade" id="createProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SKU (Kode Produk) <span class="text-danger">*</span></label>
                                <input type="text" name="sku" class="form-control" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                <select name="unit" class="form-select" required>
                                    <option value="">-- Pilih Satuan --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit }}">{{ $unit }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga Modal <span class="text-danger">*</span></label>
                                <input type="text" name="purchase_price" class="form-control price-input" inputmode="numeric" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                                <input type="text" name="selling_price" class="form-control price-input" inputmode="numeric" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Awal <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" value="0" min="0" step="0.001" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimal Stok <span class="text-danger">*</span></label>
                                <input type="number" name="min_stock" class="form-control" value="5" min="0" step="0.001" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gambar Produk</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-center">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_new" checked>
                                    <label class="form-check-label" for="is_active_new">Produk Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    <!-- END MODAL TAMBAH PRODUK -->

    <!-- SCRIPT REALTIME SEARCH & FORMAT RUPIAH -->
    <script>
        // Fungsi Format Ribuan
        function formatRibuan(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Terapkan Format Ribuan pada semua input harga (Edit & Tambah)
            document.querySelectorAll('.price-input').forEach(input => {
                if (input.value) input.value = formatRibuan(input.value);
                input.addEventListener('input', function() {
                    this.value = formatRibuan(this.value);
                });
            });

            // 2. Pencarian Realtime & Filter Kategori
            const searchInput = document.getElementById('search-product');
            const filterCategory = document.getElementById('filter-category');
            const tableRows = document.querySelectorAll('.product-row');

            function filterTable() {
                const keyword = searchInput.value.toLowerCase();
                const category = filterCategory.value.toLowerCase();

                tableRows.forEach(row => {
                    const name = row.getAttribute('data-name');
                    const sku = row.getAttribute('data-sku');
                    const rowCategory = row.getAttribute('data-category');

                    const matchSearch = name.includes(keyword) || sku.includes(keyword);
                    const matchCategory = category === '' || rowCategory === category;

                    if (matchSearch && matchCategory) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', filterTable);
            filterCategory.addEventListener('change', filterTable);
        });
    </script>
@endsection