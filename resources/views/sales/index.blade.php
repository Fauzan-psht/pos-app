@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Riwayat Transaksi</h4>
    </div>

    <!-- FORM PENCARIAN & FILTER -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="date" id="filter-start-date" class="form-control">
                </div>
                <div class="col-md-3">
                    <input type="date" id="filter-end-date" class="form-control">
                </div>
                <div class="col-md-4">
                    <!-- PASTIKAN ID-NYA search-invoice -->
                    <input type="text" id="search-invoice" class="form-control" placeholder="Cari nomor invoice...">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-secondary w-100" id="reset-filter">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL RIWAYAT TRANSAKSI -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Invoice</th>
                            <th>Tanggal</th>
                            <th>Kasir</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transaction-table-body">
                        @forelse($sales as $sale)
                            <!-- PASTIKAN ATRIBUT data-* ADA DI SINI -->
                            <tr class="transaction-row" 
                                data-invoice="{{ strtolower($sale->invoice_number) }}"
                                data-date="{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') }}"
                                data-customer="{{ strtolower($sale->customer ? $sale->customer->name : 'umum') }}">
                                
                                <td class="ps-4 fw-bold text-primary">{{ $sale->invoice_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td>
                                <td>{{ $sale->user->name }}</td>
                                <td>{{ $sale->customer ? $sale->customer->name : 'Umum' }}</td>
                                <td class="fw-bold">@rupiah($sale->total)</td>
                                <td>
                                    @if($sale->status === 'paid')
                                        <span class="badge bg-success">Sukses</span>
                                    @else
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row">
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="p-3 border-top" id="pagination-links">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-invoice');
    const startDateInput = document.getElementById('filter-start-date');
    const endDateInput = document.getElementById('filter-end-date');
    const resetBtn = document.getElementById('reset-filter');
    const rows = document.querySelectorAll('.transaction-row');
    const paginationLinks = document.getElementById('pagination-links');

    function filterTable() {
        const searchKeyword = searchInput.value.toLowerCase().trim();
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        let visibleCount = 0;

        rows.forEach(row => {
            // Ambil data dari atribut
            const invoice = row.getAttribute('data-invoice') || '';
            const rowDate = row.getAttribute('data-date') || '';
            
            // LOGIKA PENCARIAN: Cek invoice, kasir, atau pelanggan
            // Gua tambahin pengecekan ke nama kasir & pelanggan biar lebih enak
            const rowText = row.innerText.toLowerCase(); 
            const matchSearch = rowText.includes(searchKeyword);

            // Logika Filter Tanggal
            let matchDate = true;
            if (startDate && rowDate < startDate) matchDate = false;
            if (endDate && rowDate > endDate) matchDate = false;

            // Tampilkan/Sembunyikan baris
            if (matchSearch && matchDate) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Handle pesan "Tidak ditemukan"
        const emptyRow = document.getElementById('empty-row');
        if (visibleCount === 0 && rows.length > 0) {
            if (!emptyRow) {
                const tr = document.createElement('tr');
                tr.id = 'no-result-row';
                tr.innerHTML = '<td colspan="7" class="text-center py-4 text-muted">Data tidak ditemukan.</td>';
                document.getElementById('transaction-table-body').appendChild(tr);
            }
        } else {
            const noResultRow = document.getElementById('no-result-row');
            if (noResultRow) noResultRow.remove();
        }

        // Sembunyiin pagination pas nyari
        if (searchKeyword !== '' || startDate !== '' || endDate !== '') {
            paginationLinks.style.display = 'none';
        } else {
            paginationLinks.style.display = 'block';
        }
    }

    // Event Listener
    searchInput.addEventListener('input', filterTable);
    startDateInput.addEventListener('change', filterTable);
    endDateInput.addEventListener('change', filterTable);

    // Reset
    resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        startDateInput.value = '';
        endDateInput.value = '';
        filterTable();
    });
});
</script>
@endpush