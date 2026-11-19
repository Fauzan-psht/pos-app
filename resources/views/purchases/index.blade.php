@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Riwayat Stok Masuk</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPurchaseModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Stok Masuk
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>No. Dokumen</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Total Belanja</th>
                        <th>Dicatat Oleh</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->purchase_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                            <td>{{ $purchase->supplier->name }}</td>
                            <td class="text-end">@rupiah($purchase->total_amount)</td>
                            <td>{{ $purchase->user->name }}</td>
                            <td class="text-center">
                                <!-- Tombol memicu modal detail -->
                                <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#detailPurchaseModal{{ $purchase->id }}">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Belum ada data stok masuk.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $purchases->links() }}
        </div>
    </div>

    <!-- ============================================ -->
    <!-- AREA MODAL (DITARUH DI LUAR TABEL BIAR RAPI) -->
    <!-- ============================================ -->

    <!-- 1. MODAL DETAIL (Looping di sini, tapi di luar <table>) -->
    @foreach($purchases as $purchase)
        <div class="modal fade" id="detailPurchaseModal{{ $purchase->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i> Detail Stok Masuk: {{ $purchase->purchase_number }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Supplier:</strong> {{ $purchase->supplier->name }}</p>
                                <p class="mb-1"><strong>Tanggal Masuk:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d F Y') }}</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p class="mb-1"><strong>Dicatat Oleh:</strong> {{ $purchase->user->name }}</p>
                                <p class="mb-1"><strong>Catatan:</strong> {{ $purchase->note ?? '-' }}</p>
                            </div>
                        </div>

                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-end">Harga Beli</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }} ({{ $item->product->sku }})</td>
                                        <td class="text-end">@rupiah($item->purchase_price)</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">@rupiah($item->subtotal)</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total Belanja:</th>
                                    <th class="text-end">@rupiah($purchase->total_amount)</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- 2. MODAL TAMBAH STOK MASUK (FULL FORM + DYNAMIC ROWS) -->
    <div class="modal fade" id="createPurchaseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-box-arrow-in-down me-2"></i> Tambah Stok Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
                    @csrf
                    <div class="modal-body">
                        <!-- Header Form -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Catatan</label>
                                <input type="text" name="note" class="form-control" placeholder="Opsional">
                            </div>
                        </div>

                        <!-- Daftar Item -->
                        <h6 class="fw-bold mb-3">Daftar Item</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40%">Produk</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="20%">Harga Beli (Rp)</th>
                                        <th width="20%">Subtotal (Rp)</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    <tr class="item-row">
                                        <td>
                                            <select name="items[0][product_id]" class="form-select product-select" required>
                                                <option value="">-- Pilih Produk --</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">{{ $product->name }} ({{ $product->sku }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][quantity]" class="form-control qty-input" value="1" min="1" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][purchase_price]" class="form-control price-input" value="0" inputmode="numeric" required>
                                        </td>
                                        <td class="text-end subtotal-text">0</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-row" disabled><i class="bi bi-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <button type="button" class="btn btn-success btn-sm mt-2" id="addRowBtn">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Baris
                        </button>

                        <hr>
                        <div class="d-flex justify-content-end align-items-center">
                            <h5 class="mb-0 me-3">Total:</h5>
                            <h5 class="mb-0 fw-bold" id="grandTotal">Rp 0</h5>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Stok Masuk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SCRIPT DYNAMIC ROWS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let rowIndex = 1;
            const itemsBody = document.getElementById('itemsBody');
            const addRowBtn = document.getElementById('addRowBtn');
            const grandTotalEl = document.getElementById('grandTotal');

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
                return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            }

            function calculateRow(row) {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const priceInput = row.querySelector('.price-input');
                const price = parseFloat(priceInput.value.replace(/\./g, '')) || 0;
                const subtotal = qty * price;
                row.querySelector('.subtotal-text').innerText = formatRibuan(subtotal.toString());
                return subtotal;
            }

            function calculateGrandTotal() {
                let total = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    total += calculateRow(row);
                });
                grandTotalEl.innerText = 'Rp ' + formatRibuan(total.toString());
            }

            itemsBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                    if (e.target.classList.contains('price-input')) {
                        e.target.value = formatRibuan(e.target.value);
                    }
                    calculateGrandTotal();
                }
            });

            itemsBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select')) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    if (price) {
                        const row = e.target.closest('.item-row');
                        row.querySelector('.price-input').value = formatRibuan(price);
                        calculateGrandTotal();
                    }
                }
            });

            addRowBtn.addEventListener('click', function() {
                const newRow = document.createElement('tr');
                newRow.classList.add('item-row');
                newRow.innerHTML = `
                    <td>
                        <select name="items[${rowIndex}][product_id]" class="form-select product-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">{{ $product->name }} ({{ $product->sku }})</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${rowIndex}][quantity]" class="form-control qty-input" value="1" min="1" required>
                    </td>
                    <td>
                        <input type="text" name="items[${rowIndex}][purchase_price]" class="form-control price-input" value="0" inputmode="numeric" required>
                    </td>
                    <td class="text-end subtotal-text">0</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                    </td>
                `;
                itemsBody.appendChild(newRow);
                rowIndex++;
                toggleRemoveButtons();
            });

            itemsBody.addEventListener('click', function(e) {
                if (e.target.closest('.remove-row')) {
                    e.target.closest('.item-row').remove();
                    calculateGrandTotal();
                    toggleRemoveButtons();
                }
            });

            function toggleRemoveButtons() {
                const rows = document.querySelectorAll('.item-row');
                const removeBtns = document.querySelectorAll('.remove-row');
                if (rows.length === 1) {
                    removeBtns[0].disabled = true;
                } else {
                    removeBtns.forEach(btn => btn.disabled = false);
                }
            }

            calculateGrandTotal();
            toggleRemoveButtons();
        });
    </script>
@endsection