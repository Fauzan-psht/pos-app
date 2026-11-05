@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Tambah Stok Masuk</h4>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}">
                        @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <input type="text" name="note" class="form-control" value="{{ old('note') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="mb-3">Daftar Item</h5>
                @if($errors->has('items'))
                    <div class="alert alert-danger">{{ $errors->first('items') }}</div>
                @endif

                <table class="table table-bordered" id="items-table">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th width="150">Jumlah</th>
                            <th width="200">Harga Beli (Rp)</th>
                            <th width="200">Subtotal (Rp)</th>
                            <th width="50">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        {{-- Baris pertama --}}
                        <tr>
                            <td>
                                <select name="items[0][product_id]" class="form-select" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="items[0][quantity]" class="form-control quantity" value="1" min="1" required></td>
                            <td><input type="number" name="items[0][purchase_price]" class="form-control price" value="0" min="0" required></td>
                            <td class="subtotal text-end pt-2">0</td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row" disabled><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                
                <button type="button" class="btn btn-success btn-sm" id="add-row"><i class="bi bi-plus-circle"></i> Tambah Baris</button>
            </div>
            <div class="card-footer bg-white text-end">
                <h4>Total: <span id="grand-total">Rp 0</span></h4>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Simpan Stok Masuk</button>
    </form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowIndex = 1;
        const itemsBody = document.getElementById('items-body');
        const addRowBtn = document.getElementById('add-row');

        // Fungsi Hitung Subtotal & Grand Total
        function calculateTotals() {
            let grandTotal = 0;
            document.querySelectorAll('#items-body tr').forEach(row => {
                const qty = parseFloat(row.querySelector('.quantity').value) || 0;
                const price = parseFloat(row.querySelector('.price').value) || 0;
                const subtotal = qty * price;
                
                row.querySelector('.subtotal').innerText = new Intl.NumberFormat('id-ID').format(subtotal);
                grandTotal += subtotal;
            });
            document.getElementById('grand-total').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
        }

        // Fungsi Clone Baris
        addRowBtn.addEventListener('click', function() {
            const newRow = itemsBody.rows[0].cloneNode(true);
            newRow.innerHTML = newRow.innerHTML.replace(/items\[0\]/g, `items[${rowIndex}]`);
            
            // Reset nilai input
            newRow.querySelector('.quantity').value = 1;
            newRow.querySelector('.price').value = 0;
            newRow.querySelector('.subtotal').innerText = '0';
            newRow.querySelector('.remove-row').disabled = false;
            
            // Tampilkan produk
            const select = newRow.querySelector('select');
            select.value = '';

            itemsBody.appendChild(newRow);
            rowIndex++;
        });

        // Event Listener untuk Hapus Baris & Hitung Otomatis
        itemsBody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                e.target.closest('tr').remove();
                calculateTotals();
            }
        });

        itemsBody.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity') || e.target.classList.contains('price')) {
                calculateTotals();
            }
        });
    });
</script>
@endpush