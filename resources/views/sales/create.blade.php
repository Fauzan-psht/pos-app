@extends('layouts.pos')

@section('content')
<form action="{{ route('sales.store') }}" method="POST" id="pos-form">
    @csrf
    <div class="row">
        <!-- KIRI: Daftar Produk -->
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-body">
                    <input type="text" id="search-product" class="form-control" placeholder="Cari nama produk atau SKU...">
                </div>
            </div>
            <div class="row product-list" id="product-container">
                @foreach($products as $product)
                    <div class="col-md-4 mb-3 product-item" data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku) }}">
                        <div class="card product-card h-100" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $product->stock }})">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1" style="font-size: 14px;">{{ $product->name }}</h6>
                                <small class="text-muted d-block">{{ $product->sku }}</small>
                                <strong class="text-primary">@rupiah($product->selling_price)</strong>
                                <small class="d-block text-muted">Stok: {{ $product->stock }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- KANAN: Keranjang & Pembayaran -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary text-white">Keranjang Belanja</div>
                <div class="card-body cart-list" id="cart-body">
                    <!-- Item keranjang akan masuk ke sini via JS -->
                    <div class="text-center text-muted mt-5" id="empty-cart">Keranjang kosong. Klik produk di kiri.</div>
                </div>
                <div class="card-footer bg-white">
                    <table class="table table-sm table-borderless mb-2">
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-end" id="subtotal-text">Rp 0</td>
                        </tr>
                        <tr>
                            <th>Diskon (Rp)</th>
                            <td>
                                <input type="number" name="discount" id="discount-input" class="form-control form-control-sm text-end" value="0" min="0">
                            </td>
                        </tr>
                        <tr class="border-top">
                            <th><h5>Total</h5></th>
                            <td class="text-end"><h5 id="total-text">Rp 0</h5></td>
                        </tr>
                    </table>

                    <hr>
                    <div class="mb-2">
                        <label>Pelanggan</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">Pelanggan Umum</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Metode Bayar</label>
                        <select name="payment_method" id="payment-method" class="form-select form-select-sm">
                            <option value="tunai">Tunai</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Uang Diterima (Rp)</label>
                        <input type="number" name="paid_amount" id="paid-input" class="form-control form-control-lg text-end" value="0" min="0">
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <strong>Kembalian:</strong>
                        <h4 class="mb-0 text-success" id="change-text">Rp 0</h4>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100" id="submit-btn" disabled>Simpan Transaksi</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let cart = {};

    // Fungsi Tambah ke Keranjang
    function addToCart(id, name, price, maxStock) {
        if (cart[id]) {
            if (cart[id].qty < maxStock) {
                cart[id].qty++;
            } else {
                alert('Stok tidak mencukupi!');
            }
        } else {
            cart[id] = { id, name, price, qty: 1, maxStock };
        }
        renderCart();
    }

    // Fungsi Hapus dari Keranjang
    function removeFromCart(id) {
        delete cart[id];
        renderCart();
    }

    // Fungsi Ubah Qty
    function updateQty(id, newQty) {
        let qty = parseInt(newQty);
        if (isNaN(qty) || qty < 1) return;
        
        if (qty > cart[id].maxStock) {
            alert('Melebihi batas stok!');
            qty = cart[id].maxStock;
        }
        cart[id].qty = qty;
        renderCart();
    }

    // Render Keranjang ke HTML
    function renderCart() {
        const container = document.getElementById('cart-body');
        const emptyText = document.getElementById('empty-cart');
        container.innerHTML = '';

        let items = Object.values(cart);
        if (items.length === 0) {
            container.innerHTML = '<div class="text-center text-muted mt-5" id="empty-cart">Keranjang kosong. Klik produk di kiri.</div>';
            document.getElementById('submit-btn').disabled = true;
            calculateTotal();
            return;
        }

        document.getElementById('submit-btn').disabled = false;

        items.forEach(item => {
            const subtotal = item.price * item.qty;
            const row = document.createElement('div');
            row.className = 'd-flex justify-content-between align-items-center mb-2 border-bottom pb-2';
            row.innerHTML = `
                <div style="flex: 1;">
                    <div class="fw-bold" style="font-size: 14px;">${item.name}</div>
                    <small class="text-muted">@rupiah(${item.price})</small>
                </div>
                <div class="d-flex align-items-center mx-2">
                    <input type="number" name="items[${item.id}][quantity]" value="${item.qty}" min="1" max="${item.maxStock}" 
                           class="form-control form-control-sm text-center" style="width: 60px;" 
                           onchange="updateQty(${item.id}, this.value)">
                    <input type="hidden" name="items[${item.id}][product_id]" value="${item.id}">
                </div>
                <div class="text-end" style="width: 80px;">
                    <div class="fw-bold">${formatRupiah(subtotal)}</div>
                </div>
                <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeFromCart(${item.id})"><i class="bi bi-x"></i></button>
            `;
            container.appendChild(row);
        });

        calculateTotal();
    }

    // Hitung Subtotal, Total, dan Kembalian
    function calculateTotal() {
        let subtotal = 0;
        Object.values(cart).forEach(item => {
            subtotal += item.price * item.qty;
        });

        let discount = parseFloat(document.getElementById('discount-input').value) || 0;
        let total = subtotal - discount;
        if (total < 0) total = 0;

        let paid = parseFloat(document.getElementById('paid-input').value) || 0;
        let change = paid - total;

        document.getElementById('subtotal-text').innerText = formatRupiah(subtotal);
        document.getElementById('total-text').innerText = formatRupiah(total);
        
        const changeText = document.getElementById('change-text');
        if (change < 0) {
            changeText.innerText = 'Kurang ' + formatRupiah(Math.abs(change));
            changeText.className = 'mb-0 text-danger';
            document.getElementById('submit-btn').disabled = true;
        } else {
            changeText.innerText = formatRupiah(change);
            changeText.className = 'mb-0 text-success';
            if(Object.keys(cart).length > 0) document.getElementById('submit-btn').disabled = false;
        }
    }

    // Event Listener Input
    document.getElementById('discount-input').addEventListener('input', calculateTotal);
    document.getElementById('paid-input').addEventListener('input', calculateTotal);

    // Logika untuk metode bayar QRIS/Transfer
    document.getElementById('payment-method').addEventListener('change', function() {
        const paidInput = document.getElementById('paid-input');
        if (this.value !== 'tunai') {
            let discount = parseFloat(document.getElementById('discount-input').value) || 0;
            let subtotal = 0;
            Object.values(cart).forEach(item => subtotal += item.price * item.qty);
            paidInput.value = subtotal - discount; // Otomatis pas
            paidInput.readOnly = true;
        } else {
            paidInput.readOnly = false;
        }
        calculateTotal();
    });

    // Fitur Pencarian Produk
    document.getElementById('search-product').addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.getAttribute('data-name');
            const sku = item.getAttribute('data-sku');
            if (name.includes(keyword) || sku.includes(keyword)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Helper Format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }
</script>
@endpush