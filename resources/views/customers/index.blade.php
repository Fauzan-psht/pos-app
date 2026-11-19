@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Data Pelanggan</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCustomerModal">
            <i class="bi bi-person-plus me-1"></i> Tambah Pelanggan
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <!-- Tabel ini yang bakal diurusin DataTables -->
            <table class="table table-hover align-middle" id="customerTable" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Email</th>
                        <th>Total Transaksi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- KOSONGIN. Nanti diisi otomatis sama JavaScript DataTables -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL TAMBAH PELANGGAN -->
    <!-- ============================================ -->
    <div class="modal fade" id="createCustomerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i> Tambah Pelanggan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No HP (Opsional)</label>
                            <input type="text" name="phone" class="form-control" placeholder="Contoh: 081234567890">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email (Opsional)</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat (Opsional)</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<!-- Script DataTables Server-Side -->
<script>
    $(document).ready(function() {
        $('#customerTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('customers.index') }}",
            columns: [
                { data: 'name', name: 'name', className: 'fw-bold' },
                { data: 'phone', name: 'phone' },
                { data: 'email', name: 'email' },
                { data: 'sales_badge', name: 'sales_count', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang ditemukan",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    });
</script>
@endpush