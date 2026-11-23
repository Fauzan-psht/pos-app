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
                <div class="col-md-3">
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
                <table class="table table-hover align-middle mb-0" id="salesTable" style="width: 100%;">
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
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(function () {
    const table = $('#salesTable').DataTable({
        ordering: false,
        ajax: {
            url: "{{ route('sales.index') }}",
            data: function (data) {
                data.start_date = $('#filter-start-date').val();
                data.end_date = $('#filter-end-date').val();
            }
        },
        columns: [
            { data: 'invoice_number', name: 'invoice_number', className: 'ps-4 fw-bold text-primary' },
            { data: 'sale_date', name: 'sale_date' },
            { data: 'user.name', name: 'user.name' },
            { data: 'customer_name', name: 'customer.name' },
            { data: 'total', name: 'total', className: 'fw-bold' },
            { data: 'status', name: 'status', searchable: false },
            { data: 'action', orderable: false, searchable: false, className: 'text-center' }
        ]
    });

    $('#filter-start-date, #filter-end-date').on('change', function () {
        table.ajax.reload();
    });

    $('#reset-filter').on('click', function () {
        $('#filter-start-date, #filter-end-date').val('');
        table.ajax.reload();
    });
});
</script>
@endpush