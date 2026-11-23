@extends('layouts.pos')

@section('content')
<style>
    /* Menghilangkan panah atas/bawah pada input angka */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }

    /* MENGATUR KERANJANG AGAR BISA DI-SCROLL DAN TOMBOL SELALU TERLIHAT */
    .cart-wrapper {
        max-height: calc(100vh - 100px);
        display: flex;
        flex-direction: column;
    }
    .cart-items-area {
        flex-grow: 1;
        overflow-y: auto;
        min-height: 150px;
    }
    .cart-footer-area {
        flex-shrink: 0;
    }

    /* GAMBAR PRODUK SERAGAM */
    .product-image-container {
        height: 90px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }
    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Memastikan Kolom Kiri memenuhi tinggi layar */
    .product-col {
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 100px);
    }

    .qty-stepper {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>

<form action="{{ route('sales.store') }}" method="POST" id="pos-form">
    @csrf
    <div class="row h-100">
        <!-- KIRI: Daftar Produk -->
        <div class="col-md-7 mb-3 product-col">
            <div class="card mb-2 shadow-sm border-0 flex-shrink-0">
                <div class="card-body py-2">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="search-product" class="form-control border-start-0" placeholder="Cari nama produk atau SKU...">
                    </div>
                </div>
            </div>

            <div class="row g-2" id="product-container" style="flex-grow: 1; overflow-y: auto; align-content: flex-start;">
                <div class="col-12 text-center text-muted py-4">Memuat produk...</div>
            </div>
            <div id="product-pagination" class="d-flex justify-content-between align-items-center pt-2 flex-shrink-0"></div>
        </div>

        <!-- KANAN: Keranjang & Pembayaran -->
        <div class="col-md-5 mb-3">
            <div class="card shadow-sm border-0 cart-wrapper">
                <div class="card-header bg-primary text-white py-3 flex-shrink-0">
                    <h6 class="mb-0"><i class="bi bi-cart3 me-2"></i> Keranjang Belanja</h6>
                </div>

                <div class="card-body p-2 cart-items-area" id="cart-body">
                    <div class="text-center text-muted mt-5" id="empty-cart">
                        <i class="bi bi-cart-x" style="font-size: 40px; color: #ccc;"></i>
                        <p class="mt-2 mb-0" style="font-size: 14px;">Keranjang kosong.<br>Klik produk di kiri.</p>
                    </div>
                </div>

                <div class="card-footer bg-white pt-3 cart-footer-area">
                    <table class="table table-sm table-borderless mb-2">
                        <tr>
                            <th class="text-muted">Subtotal</th>
                            <td class="text-end fw-bold" id="subtotal-text">Rp 0</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Diskon (Rp)</th>
                            <td>
                                <input type="text" name="discount" id="discount-input" class="form-control form-control-sm text-end" value="0" inputmode="numeric">
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
                        <input type="text" name="paid_amount" id="paid-input" class="form-control form-control-lg text-end fw-bold text-success" value="0" inputmode="numeric">
                    </div>

                    <div class="mb-2 p-2 bg-light rounded d-flex justify-content-between align-items-center">
                        <strong class="text-muted">Kembalian:</strong>
                        <h5 class="mb-0 text-success fw-bold" id="change-text">Rp 0</h5>
                    </div>

                    <button type="button" class="btn btn-success btn-lg w-100 shadow-sm fw-bold" id="submit-btn-tunai" disabled>
                        <i class="bi bi-check-circle me-1"></i> Simpan Transaksi
                    </button>

                    <button type="button" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold d-none" id="submit-btn-qris">
                        <i class="bi bi-qr-code-scan me-1"></i> Bayar dengan QRIS
                    </button>

                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 mt-1 btn-sm">Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- MODAL QRIS -->
<div class="modal fade" id="qrisModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-qr-code-scan me-2"></i> Pembayaran QRIS</h5>
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
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-bank me-2"></i> Instruksi Transfer Bank</h5>
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
    let productPage = 1;
    let productRequestId = 0;
    let productSearchTimeout;

    const productEndpoint = @json(route('sales.products'));

    async function loadProducts(page = 1) {
        const requestId = ++productRequestId;
        const container = document.getElementById('product-container');
        const pagination = document.getElementById('product-pagination');
        const search = document.getElementById('search-product').value.trim();
        const query = new URLSearchParams({ page: String(page), search });

        container.replaceChildren();
        pagination.replaceChildren();
        const loadingMessage = document.createElement('div');
        loadingMessage.className = 'col-12 text-center text-muted py-4';
        loadingMessage.textContent = 'Memuat produk...';
        container.appendChild(loadingMessage);

        try {
            const response = await fetch(`${productEndpoint}?${query.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`Permintaan produk gagal (${response.status}).`);
            }

            const result = await response.json();
            if (requestId !== productRequestId) {
                return;
            }

            productPage = result.current_page;
            container.replaceChildren();

            if (result.data.length === 0) {
                const emptyMessage = document.createElement('div');
                emptyMessage.className = 'col-12 text-center text-muted py-4';
                emptyMessage.textContent = 'Produk tidak ditemukan.';
                container.appendChild(emptyMessage);
            } else {
                result.data.forEach(product => {
                    const column = document.createElement('div');
                    column.className = 'col-md-3';

                    const card = document.createElement('div');
                    card.className = 'card product-card h-100 shadow-sm border-0';
                    card.addEventListener('click', () => addToCart(
                        product.id,
                        product.name,
                        Number(product.selling_price),
                        Number(product.stock),
                        product.image_url || ''
                    ));

                    const imageContainer = document.createElement('div');
                    imageContainer.className = 'product-image-container rounded-top';
                    if (product.image_url) {
                        const image = document.createElement('img');
                        image.src = product.image_url;
                        image.alt = product.name;
                        imageContainer.appendChild(image);
                    } else {
                        const icon = document.createElement('i');
                        icon.className = 'bi bi-box-seam text-secondary';
                        icon.style.fontSize = '30px';
                        imageContainer.appendChild(icon);
                    }

                    const body = document.createElement('div');
                    body.className = 'card-body p-2 d-flex flex-column';

                    const name = document.createElement('h6');
                    name.className = 'card-title mb-1';
                    name.style.cssText = 'font-size: 12px; font-weight: 600; height: 32px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;';
                    name.textContent = product.name;

                    const details = document.createElement('div');
                    details.className = 'mt-auto';

                    const price = document.createElement('strong');
                    price.className = 'text-primary d-block mb-1';
                    price.style.fontSize = '13px';
                    price.textContent = formatRupiah(product.selling_price);

                    const metadata = document.createElement('div');
                    metadata.className = 'd-flex justify-content-between align-items-center';

                    const stock = document.createElement('small');
                    stock.className = 'text-muted';
                    stock.style.fontSize = '10px';
                    stock.textContent = `Stok: ${Number(product.stock).toLocaleString('id-ID', { maximumFractionDigits: 3 })} ${product.unit}`;

                    const sku = document.createElement('span');
                    sku.className = 'badge bg-light text-dark border';
                    sku.style.fontSize = '9px';
                    sku.textContent = product.sku;

                    metadata.append(stock, sku);
                    details.append(price, metadata);
                    body.append(name, details);
                    card.append(imageContainer, body);
                    column.appendChild(card);
                    container.appendChild(column);
                });
            }

            renderProductPagination(result.current_page, result.last_page);
        } catch (error) {
            if (requestId !== productRequestId) {
                return;
            }

            console.error('Gagal memuat produk kasir:', error);
            container.replaceChildren();
            const errorMessage = document.createElement('div');
            errorMessage.className = 'col-12 text-center text-danger py-4';
            errorMessage.textContent = 'Produk gagal dimuat. Silakan coba lagi.';
            container.appendChild(errorMessage);

            const retryButton = document.createElement('button');
            retryButton.type = 'button';
            retryButton.className = 'btn btn-sm btn-outline-primary';
            retryButton.textContent = 'Coba lagi';
            retryButton.addEventListener('click', () => loadProducts(productPage));
            pagination.replaceChildren(retryButton);
        }
    }

    function renderProductPagination(currentPage, lastPage) {
        const pagination = document.getElementById('product-pagination');
        pagination.replaceChildren();
        if (lastPage <= 1) {
            return;
        }

        const previousButton = document.createElement('button');
        previousButton.type = 'button';
        previousButton.className = 'btn btn-sm btn-outline-secondary';
        previousButton.textContent = 'Sebelumnya';
        previousButton.disabled = currentPage <= 1;
        previousButton.addEventListener('click', () => loadProducts(currentPage - 1));

        const pageLabel = document.createElement('small');
        pageLabel.className = 'text-muted';
        pageLabel.textContent = `Halaman ${currentPage} dari ${lastPage}`;

        const nextButton = document.createElement('button');
        nextButton.type = 'button';
        nextButton.className = 'btn btn-sm btn-outline-secondary';
        nextButton.textContent = 'Berikutnya';
        nextButton.disabled = currentPage >= lastPage;
        nextButton.addEventListener('click', () => loadProducts(currentPage + 1));

        pagination.append(previousButton, pageLabel, nextButton);
    }

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

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[character]);
    }

    const paidInputField = document.getElementById('paid-input');
    paidInputField.addEventListener('input', function() {
        this.value = formatRibuan(this.value);
        calculateTotal();
    });

    const discountInputField = document.getElementById('discount-input');
    discountInputField.addEventListener('input', function() {
        this.value = formatRibuan(this.value);
        calculateTotal();
    });

    function renderCart() {
        const container = document.getElementById('cart-body');
        container.innerHTML = '';
        let items = Object.values(cart);

        if (items.length === 0) {
            container.innerHTML = `<div class="text-center text-muted mt-5" id="empty-cart"><i class="bi bi-cart-x" style="font-size: 40px; color: #ccc;"></i><p class="mt-2 mb-0" style="font-size: 14px;">Keranjang kosong.<br>Klik produk di kiri.</p></div>`;
        } else {
            items.forEach(item => {
                const itemSubtotal = item.price * item.qty;
                const row = document.createElement('div');
                row.className = 'd-flex justify-content-between align-items-center mb-2 border-bottom pb-2';
                let imageHtml = item.imageUrl ? '<img src="' + escapeHtml(item.imageUrl) + '" width="40" height="40" class="rounded me-2 object-fit-cover border">' : '<div class="bg-light rounded me-2 border d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;"><i class="bi bi-box text-muted"></i></div>';
                const quantityStep = Number.isInteger(item.maxStock) ? 1 : 0.001;
                const minimumQuantity = quantityStep;
                row.innerHTML =
                    '<div class="d-flex align-items-center" style="flex: 1; padding-right: 10px;">' + imageHtml +
                        '<div><div class="fw-bold text-dark" style="font-size: 13px;">' + escapeHtml(item.name) + '</div><small class="text-muted" style="font-size: 12px;">' + formatRupiah(item.price) + '</small></div></div>' +
                    '<div class="d-flex align-items-center gap-1" style="width: 100px;">' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary qty-stepper" data-id="' + item.id + '" data-delta="' + -quantityStep + '"><i class="bi bi-dash"></i></button>' +
                        '<input type="number" name="items[' + item.id + '][quantity]" value="' + item.qty + '" min="' + minimumQuantity + '" max="' + item.maxStock + '" step="' + quantityStep + '" class="form-control form-control-sm text-center qty-input" style="width: 45px;" data-id="' + item.id + '">' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary qty-stepper" data-id="' + item.id + '" data-delta="' + quantityStep + '"><i class="bi bi-plus"></i></button>' +
                        '<input type="hidden" name="items[' + item.id + '][product_id]" value="' + item.id + '">' +
                    '</div>' +
                    '<div class="text-end" style="width: 85px;"><div class="fw-bold text-dark" style="font-size: 13px;">' + formatRupiah(itemSubtotal) + '</div></div>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-btn border-0" data-id="' + item.id + '"><i class="bi bi-trash"></i></button>';
                container.appendChild(row);
            });
        }
        calculateTotal();
    }

    function calculateTotal() {
        let subtotal = 0;
        Object.values(cart).forEach(item => { subtotal += item.price * item.qty; });

        let discountStr = document.getElementById('discount-input').value.replace(/\./g, '');
        let discount = parseFloat(discountStr) || 0;
        let total = subtotal - discount; if (total < 0) total = 0;

        if (currentPaymentMethod !== 'tunai') {
            document.getElementById('paid-input').value = formatRibuan(total.toString());
        }

        let paidStr = document.getElementById('paid-input').value.replace(/\./g, '');
        let paid = parseFloat(paidStr) || 0;
        let change = paid - total;

        document.getElementById('subtotal-text').innerText = formatRupiah(subtotal);
        document.getElementById('total-text').innerText = formatRupiah(total);

        const changeText = document.getElementById('change-text');
        const btnTunai = document.getElementById('submit-btn-tunai');
        const btnQris = document.getElementById('submit-btn-qris');
        let itemsCount = Object.keys(cart).length;

        if (itemsCount === 0) {
            document.getElementById('paid-input').value = '0';
            document.getElementById('discount-input').value = '0';
            changeText.innerText = 'Rp 0';
            changeText.className = 'mb-0 text-success fw-bold';
            btnTunai.disabled = true; btnQris.disabled = true;
            return;
        }

        if (paid < total && currentPaymentMethod === 'tunai') {
            changeText.innerText = 'Kurang ' + formatRupiah(Math.abs(change));
            changeText.className = 'mb-0 text-danger fw-bold';
            btnTunai.disabled = true; btnQris.disabled = false;
        } else {
            changeText.innerText = formatRupiah(change);
            changeText.className = 'mb-0 text-success fw-bold';
            btnTunai.disabled = false; btnQris.disabled = false;
        }
    }

    function addToCart(id, name, price, maxStock, imageUrl) {
        if (cart[id]) {
            if (cart[id].qty < maxStock) {
                cart[id].qty = Math.min(cart[id].qty + 1, maxStock);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Tidak Cukup',
                    text: 'Jumlah melebihi batas stok yang tersedia!',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'OK'
                });
            }
        }
        else { cart[id] = { id, name, price, qty: Math.min(1, maxStock), maxStock, imageUrl }; }
        renderCart();
    }

    document.getElementById('cart-body').addEventListener('click', function(e) {
        const stepper = e.target.closest('.qty-stepper');
        if (stepper) {
            const id = stepper.getAttribute('data-id');
            const delta = parseFloat(stepper.getAttribute('data-delta'));
            const minimumQuantity = Number.isInteger(cart[id].maxStock) ? 1 : 0.001;
            let qty = Math.round((cart[id].qty + delta) * 1000) / 1000;
            if (qty < minimumQuantity) qty = minimumQuantity;

            if (qty > cart[id].maxStock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Melebihi Stok',
                    text: 'Jumlah barang melebihi stok yang tersedia!',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'OK'
                });
                qty = cart[id].maxStock;
            }
            cart[id].qty = qty;
            renderCart();
            return;
        }

        if (e.target.closest('.remove-btn')) {
            const id = e.target.closest('.remove-btn').getAttribute('data-id');
            delete cart[id]; renderCart();
        }
    });

    document.getElementById('cart-body').addEventListener('change', function(e) {
        if (e.target.classList.contains('qty-input')) {
            const id = e.target.getAttribute('data-id');
            const minimumQuantity = Number.isInteger(cart[id].maxStock) ? 1 : 0.001;
            let qty = parseFloat(e.target.value);
            if (isNaN(qty) || qty < minimumQuantity) qty = minimumQuantity;
            qty = Math.round(qty * 1000) / 1000;
            if (qty > cart[id].maxStock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Melebihi Stok',
                    text: 'Jumlah barang melebihi stok yang tersedia!',
                    confirmButtonColor: '#0d6efd',
                    confirmButtonText: 'OK'
                });
                qty = cart[id].maxStock;
            }
            cart[id].qty = qty; renderCart();
        }
    });

    document.getElementById('payment-method').addEventListener('change', function() {
        currentPaymentMethod = this.value;
        const paidInput = document.getElementById('paid-input');
        const btnTunai = document.getElementById('submit-btn-tunai');
        const btnQris = document.getElementById('submit-btn-qris');

        if (this.value !== 'tunai') {
            let discount = parseFloat(document.getElementById('discount-input').value.replace(/\./g, '')) || 0;
            let subtotal = 0;
            Object.values(cart).forEach(item => subtotal += item.price * item.qty);
            let finalTotal = subtotal - discount;
            paidInput.value = formatRibuan(finalTotal.toString());
            paidInput.readOnly = true;

            if (this.value === 'qris') {
                btnTunai.classList.add('d-none'); btnQris.classList.remove('d-none');
                btnQris.innerHTML = '<i class="bi bi-qr-code-scan me-1"></i> Bayar dengan QRIS';
            } else {
                btnTunai.classList.remove('d-none'); btnQris.classList.add('d-none');
                btnTunai.innerHTML = '<i class="bi bi-bank me-1"></i> Instruksi Transfer Bank';
            }
        } else {
            paidInput.readOnly = false; btnTunai.classList.remove('d-none');
            btnQris.classList.add('d-none'); btnTunai.innerHTML = '<i class="bi bi-check-circle me-1"></i> Simpan Transaksi';
        }
        calculateTotal();
    });

    document.getElementById('submit-btn-tunai').addEventListener('click', function() {
        if (this.disabled) return;

        if (currentPaymentMethod === 'transfer') {
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
                let minutes = Math.floor(timeLeft / 60), seconds = timeLeft % 60;
                timerElement.innerText = `Waktu tersisa: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                if (timeLeft <= 0) {
                    clearInterval(transferInterval);
                    transferModal.hide();

                    Swal.fire({
                        icon: 'error',
                        title: 'Waktu Habis!',
                        text: 'Waktu transfer telah habis. Silakan lakukan pemesanan ulang.',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Tutup'
                    }).then(() => {
                        document.getElementById('payment-method').value = 'tunai';
                        document.getElementById('payment-method').dispatchEvent(new Event('change'));
                    });
                }
            }, 1000);

        } else {
            document.getElementById('pos-form').requestSubmit();
        }
    });

    document.getElementById('confirm-transfer-btn').addEventListener('click', function() {
        clearInterval(transferInterval);
        isTransferConfirmed = true;
        document.getElementById('pos-form').requestSubmit();
    });

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
            let minutes = Math.floor(timeLeft / 60), seconds = timeLeft % 60;
            timerElement.innerText = `Waktu tersisa: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                qrisModal.hide();

                Swal.fire({
                    icon: 'error',
                    title: 'Waktu Habis!',
                    text: 'Waktu pembayaran QRIS telah habis. Silakan lakukan pemesanan ulang.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Tutup'
                }).then(() => {
                    document.getElementById('payment-method').value = 'tunai';
                    document.getElementById('payment-method').dispatchEvent(new Event('change'));
                });
            }
        }, 1000);
    });

    document.getElementById('confirm-qris-btn').addEventListener('click', function() {
        clearInterval(countdownInterval);
        document.getElementById('pos-form').requestSubmit();
    });

    document.getElementById('search-product').addEventListener('input', function() {
        clearTimeout(productSearchTimeout);
        productSearchTimeout = setTimeout(() => loadProducts(1), 250);
    });

    function formatRupiah(angka) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka); }

    loadProducts();
</script>
@endsection