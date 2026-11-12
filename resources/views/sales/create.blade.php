@extends('layouts.pos')

@section('content')
<form action="{{ route('sales.store') }}" method="POST" id="pos-form">
    @csrf
    <div class="row">
        <!-- KIRI: Daftar Produk -->
        <div class="col-md-6 mb-3">
            <div class="card mb-2 shadow-sm border-0">
                <div class="card-body py-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="search-product" class="form-control border-start-0" placeholder="Cari nama produk atau SKU...">
                    </div>
                </div>
            </div>
            
            <div class="row g-2" id="product-container" style="max-height: 68vh; overflow-y: auto; padding-bottom: 10px;">
                @foreach($products as $product)
                    <div class="col-md-4 product-item" data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku) }}">
                        <div class="card product-card h-100 shadow-sm border-0" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }}, {{ $product->stock }}, '{{ $product->image ? asset('storage/' . $product->image) : '' }}')">
                            
                            <div class="text-center pt-3 pb-2 bg-light rounded-top" style="height: 90px; overflow: hidden;">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                @else
                                    <i class="bi bi-box-seam text-secondary" style="font-size: 40px;"></i>
                                @endif
                            </div>

                            <div class="card-body p-2 d-flex flex-column">
                                <h6 class="card-title mb-1" style="font-size: 13px; font-weight: 600; height: 36px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    {{ $product->name }}
                                </h6>
                                <div class="mt-auto">
                                    <strong class="text-primary d-block mb-1" style="font-size: 14px;">@rupiah($product->selling_price)</strong>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted" style="font-size: 11px;">Stok: {{ $product->stock }}</small>
                                        <span class="badge bg-light text-dark border" style="font-size: 10px;">{{ $product->sku }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- KANAN: Keranjang & Pembayaran -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0"><i class="bi bi-cart3 me-2"></i> Keranjang Belanja</h6>
                </div>
                
                <div class="card-body p-2" id="cart-body" style="min-height: 200px; max-height: 350px; overflow-y: auto;">
                    <div class="text-center text-muted mt-5" id="empty-cart">
                        <i class="bi bi-cart-x" style="font-size: 40px; color: #ccc;"></i>
                        <p class="mt-2 mb-0" style="font-size: 14px;">Keranjang kosong.<br>Klik produk di kiri.</p>
                    </div>
                </div>
                
                <div class="card-footer bg-white pt-3">
                    <table class="table table-sm table-borderless mb-2">
                        <tr>
                            <th class="text-muted">Subtotal</th>
                            <td class="text-end fw-bold" id="subtotal-text">Rp 0</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Diskon (Rp)</th>
                            <td>
                                <input type="number" name="discount" id="discount-input" class="form-control form-control-sm text-end" value="0" min="0">
                            </td>
                        </tr>
                        <tr class="border-top">
                            <th class="pt-2"><h5 class="mb-0 fw-bold">Total</h5></th>
                            <td class="text-end pt-2"><h5 class="mb-0 fw-bold text-primary" id="total-text">Rp 0</h5></td>
                        </tr>
                    </table>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="small text-muted mb-1">Pelanggan</label>
                            <select name="customer_id" class="form-select form-select-sm">
                                <option value="">Pelanggan Umum</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted mb-1">Metode Bayar</label>
                            <select name="payment_method" id="payment-method" class="form-select form-select-sm">
                                <option value="tunai">Tunai</option>
                                <option value="qris">QRIS</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <label class="small text-muted mb-1">Uang Diterima (Rp)</label>
                        <input type="number" name="paid_amount" id="paid-input" class="form-control form-control-lg text-end fw-bold text-success" value="0" min="0">
                    </div>
                    
                    <div class="mb-3 p-2 bg-light rounded d-flex justify-content-between align-items-center">
                        <strong class="text-muted">Kembalian:</strong>
                        <h5 class="mb-0 text-success fw-bold" id="change-text">Rp 0</h5>
                    </div>

                    <!-- TOMBOL SIMPAN (TUNAI / TRANSFER) -->
                    <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm fw-bold" id="submit-btn-tunai" disabled>
                        <i class="bi bi-check-circle me-1"></i> Simpan Transaksi
                    </button>
                    
                    <!-- TOMBOL KHUSUS QRIS -->
                    <button type="button" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold d-none" id="submit-btn-qris">
                        <i class="bi bi-qr-code-scan me-1"></i> Bayar dengan QRIS
                    </button>
                    
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- MODAL QRIS -->
<div class="modal fade" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="qrisModalLabel"><i class="bi bi-qr-code-scan me-2"></i> Pembayaran QRIS</h5>
            </div>
            <div class="modal-body text-center">
                <h4 class="mb-1 fw-bold text-primary" id="qris-total-text">Rp 0</h4>
                <p class="text-muted small mb-3">Silakan scan QRIS di bawah ini</p>
                <h5 class="text-danger fw-bold mb-3" id="countdown-timer">Waktu tersisa: 01:00</h5>
                
                <img src="{{ asset('images/qris.png') }}" alt="QRIS" class="img-fluid border rounded shadow-sm mb-3" style="max-width: 300px;">
                
                <div class="alert alert-info py-2 mb-0" style="font-size: 13px;">
                    <i class="bi bi-info-circle-fill me-1"></i> Pastikan nominal pembayaran sudah sesuai.
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success px-4" id="confirm-qris-btn">Pembayaran Berhasil</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TRANSFER BANK -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="transferModalLabel"><i class="bi bi-bank me-2"></i> Instruksi Transfer Bank</h5>
            </div>
            <div class="modal-body text-center">
                <h4 class="mb-1 fw-bold text-primary" id="transfer-total-text">Rp 0</h4>
                <p class="text-muted small mb-2">Silakan transfer sesuai nominal di atas ke rekening:</p>
                
                <h5 class="text-danger fw-bold mb-3" id="transfer-timer">Waktu tersisa: 01:00</h5>

                <div class="card bg-light border-0 mb-3">
                    <div class="card-body">
                        <h5 class="fw-bold mb-1">Bank BCA</h5>
                        <h3 class="fw-bold text-dark tracking-wider mb-2">123-456-7890</h3>
                        <p class="text-muted mb-0">a.n. <strong>Toko Makmur Sentosa</strong></p>
                    </div>
                </div>
                
                <div class="alert alert-warning py-2 mb-0" style="font-size: 13px;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Konfirmasi setelah pembeli menunjukkan bukti transfer.
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success px-4" id="confirm-transfer-btn">Pembayaran Diterima</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = {};
    let countdownInterval;
    let transferInterval;
    let currentPaymentMethod = 'tunai';
    let isTransferConfirmed = false;

    function renderCart() {
        const container = document.getElementById('cart-body');
        container.innerHTML = '';
        let items = Object.values(cart);
        
        if (items.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted mt-5" id="empty-cart">
                    <i class="bi bi-cart-x" style="font-size: 40px; color: #ccc;"></i>
                    <p class="mt-2 mb-0" style="font-size: 14px;">Keranjang kosong.<br>Klik produk di kiri.</p>
                </div>`;
        } else {
            items.forEach(item => {
                const itemSubtotal = item.price * item.qty;
                const row = document.createElement('div');
                row.className = 'd-flex justify-content-between align-items-center mb-2 border-bottom pb-2';
                
                let imageHtml = item.imageUrl 
                    ? '<img src="' + item.imageUrl + '" width="40" height="40" class="rounded me-2 object-fit-cover border">' 
                    : '<div class="bg-light rounded me-2 border d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;"><i class="bi bi-box text-muted"></i></div>';

                row.innerHTML = 
                    '<div class="d-flex align-items-center" style="flex: 1; padding-right: 10px;">' +
                        imageHtml +
                        '<div>' +
                            '<div class="fw-bold text-dark" style="font-size: 13px;">' + item.name + '</div>' +
                            '<small class="text-muted" style="font-size: 12px;">' + formatRupiah(item.price) + '</small>' +
                        '</div>' +
                    '</div>' +
                    '<div class="d-flex align-items-center">' +
                        '<input type="number" name="items[' + item.id + '][quantity]" value="' + item.qty + '" min="1" max="' + item.maxStock + '" class="form-control form-control-sm text-center qty-input" style="width: 55px;" data-id="' + item.id + '">' +
                        '<input type="hidden" name="items[' + item.id + '][product_id]" value="' + item.id + '">' +
                    '</div>' +
                    '<div class="text-end" style="width: 85px;">' +
                        '<div class="fw-bold text-dark" style="font-size: 13px;">' + formatRupiah(itemSubtotal) + '</div>' +
                    '</div>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-btn border-0" data-id="' + item.id + '">' +
                        '<i class="bi bi-trash"></i>' +
                    '</button>';
                
                container.appendChild(row);
            });
        }
        calculateTotal();
    }

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
        const btnTunai = document.getElementById('submit-btn-tunai');
        const btnQris = document.getElementById('submit-btn-qris');
        let itemsCount = Object.keys(cart).length;

        if (itemsCount === 0) {
            btnTunai.disabled = true;
            btnQris.disabled = true;
        } else if (paid < total && currentPaymentMethod === 'tunai') {
            changeText.innerText = 'Kurang ' + formatRupiah(Math.abs(change));
            changeText.className = 'mb-0 text-danger fw-bold';
            btnTunai.disabled = true;
            btnQris.disabled = false;
        } else {
            changeText.innerText = formatRupiah(change);
            changeText.className = 'mb-0 text-success fw-bold';
            btnTunai.disabled = false; 
            btnQris.disabled = false;
        }
    }

    function addToCart(id, name, price, maxStock, imageUrl) {
        if (cart[id]) {
            if (cart[id].qty < maxStock) { cart[id].qty++; } else { alert('Stok tidak mencukupi!'); }
        } else {
            cart[id] = { id, name, price, qty: 1, maxStock, imageUrl };
        }
        renderCart();
    }

    document.getElementById('cart-body').addEventListener('click', function(e) {
        if (e.target.closest('.remove-btn')) {
            const id = e.target.closest('.remove-btn').getAttribute('data-id');
            delete cart[id];
            renderCart();
        }
    });

    document.getElementById('cart-body').addEventListener('change', function(e) {
        if (e.target.classList.contains('qty-input')) {
            const id = e.target.getAttribute('data-id');
            let qty = parseInt(e.target.value);
            if (isNaN(qty) || qty < 1) qty = 1;
            if (qty > cart[id].maxStock) { alert('Melebihi batas stok!'); qty = cart[id].maxStock; }
            cart[id].qty = qty;
            renderCart();
        }
    });

    document.getElementById('paid-input').addEventListener('input', calculateTotal);
    document.getElementById('discount-input').addEventListener('input', calculateTotal);
    
    document.getElementById('payment-method').addEventListener('change', function() {
        currentPaymentMethod = this.value;
        const paidInput = document.getElementById('paid-input');
        const btnTunai = document.getElementById('submit-btn-tunai');
        const btnQris = document.getElementById('submit-btn-qris');

        if (this.value !== 'tunai') {
            let discount = parseFloat(document.getElementById('discount-input').value) || 0;
            let subtotal = 0;
            Object.values(cart).forEach(item => subtotal += item.price * item.qty);
            paidInput.value = subtotal - discount; 
            paidInput.readOnly = true;

            if (this.value === 'qris') {
                btnTunai.classList.add('d-none');
                btnQris.classList.remove('d-none');
                btnQris.innerHTML = '<i class="bi bi-qr-code-scan me-1"></i> Bayar dengan QRIS';
            } else {
                btnTunai.classList.remove('d-none');
                btnQris.classList.add('d-none');
                btnTunai.innerHTML = '<i class="bi bi-bank me-1"></i> Instruksi Transfer Bank';
            }
        } else { 
            paidInput.readOnly = false; 
            btnTunai.classList.remove('d-none');
            btnQris.classList.add('d-none');
            btnTunai.innerHTML = '<i class="bi bi-check-circle me-1"></i> Simpan Transaksi';
        }
        calculateTotal();
    });

    // ==========================================
    // LOGIKA KHUSUS TRANSFER BANK & TIMER
    // ==========================================
    document.getElementById('pos-form').addEventListener('submit', function(e) {
        if (currentPaymentMethod === 'transfer' && !isTransferConfirmed) {
            e.preventDefault(); 
            
            let totalText = document.getElementById('total-text').innerText;
            document.getElementById('transfer-total-text').innerText = totalText;
            
            let transferModal = new bootstrap.Modal(document.getElementById('transferModal'));
            transferModal.show();

            let timeLeft = 60;
            const timerElement = document.getElementById('transfer-timer');
            timerElement.innerText = `Waktu tersisa: 01:00`;
            clearInterval(transferInterval);

            transferInterval = setInterval(function() {
                timeLeft--;
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                timerElement.innerText = `Waktu tersisa: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 0) {
                    clearInterval(transferInterval);
                    transferModal.hide();
                    alert('Waktu transfer habis! Silakan lakukan pemesanan ulang.');
                    document.getElementById('payment-method').value = 'tunai';
                    document.getElementById('payment-method').dispatchEvent(new Event('change'));
                }
            }, 1000);
        }
    });

    document.getElementById('confirm-transfer-btn').addEventListener('click', function() {
        clearInterval(transferInterval); 
        isTransferConfirmed = true;
        document.getElementById('pos-form').submit();
    });

    // ==========================================
    // LOGIKA KHUSUS QRIS & TIMER
    // ==========================================
    document.getElementById('submit-btn-qris').addEventListener('click', function() {
        let totalText = document.getElementById('total-text').innerText;
        document.getElementById('qris-total-text').innerText = totalText;
        
        let qrisModal = new bootstrap.Modal(document.getElementById('qrisModal'));
        qrisModal.show();

        let timeLeft = 60;
        const timerElement = document.getElementById('countdown-timer');
        timerElement.innerText = `Waktu tersisa: 01:00`;
        clearInterval(countdownInterval);

        countdownInterval = setInterval(function() {
            timeLeft--;
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            timerElement.innerText = `Waktu tersisa: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                qrisModal.hide();
                alert('Waktu pembayaran QRIS habis! Silakan lakukan pemesanan ulang.');
                document.getElementById('payment-method').value = 'tunai';
                document.getElementById('payment-method').dispatchEvent(new Event('change'));
            }
        }, 1000);
    });

    document.getElementById('confirm-qris-btn').addEventListener('click', function() {
        clearInterval(countdownInterval); 
        document.getElementById('pos-form').submit();
    });

    document.getElementById('search-product').addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.getAttribute('data-name');
            const sku = item.getAttribute('data-sku');
            item.style.display = (name.includes(keyword) || sku.includes(keyword)) ? 'block' : 'none';
        });
    });

    function formatRupiah(angka) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka); }
</script>
@endsection