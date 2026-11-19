@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Data Supplier</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
            Tambah Supplier
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Nama Supplier</th>
                        <th>Contact Person</th>
                        <th>No HP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact_person ?? '-' }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $supplier->id }}">
                                    Edit
                                </button>

                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus supplier ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Belum ada supplier.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $suppliers->links() }}
        </div>
    </div>

    @foreach($suppliers as $supplier)
        <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Edit Supplier: {{ $supplier->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_supplier_id" value="{{ $supplier->id }}">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Supplier</label>
                                <input type="text" name="name" class="form-control @if(old('_supplier_id') == $supplier->id) @error('name') is-invalid @enderror @endif"
                                    value="{{ old('_supplier_id') == $supplier->id ? old('name') : $supplier->name }}" required>
                                @if(old('_supplier_id') == $supplier->id)
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control"
                                    value="{{ old('_supplier_id') == $supplier->id ? old('contact_person') : $supplier->contact_person }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No HP / Telp</label>
                                <input type="text" name="phone" class="form-control @if(old('_supplier_id') == $supplier->id) @error('phone') is-invalid @enderror @endif"
                                    value="{{ old('_supplier_id') == $supplier->id ? old('phone') : $supplier->phone }}" required>
                                @if(old('_supplier_id') == $supplier->id)
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="address" class="form-control" rows="3">{{ old('_supplier_id') == $supplier->id ? old('address') : $supplier->address }}</textarea>
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
    @endforeach

    <div class="modal fade" id="createSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Tambah Supplier Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_supplier_id" value="create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Supplier</label>
                            <input type="text" name="name" class="form-control @if(old('_supplier_id') === 'create') @error('name') is-invalid @enderror @endif"
                                value="{{ old('_supplier_id') === 'create' ? old('name') : '' }}" required>
                            @if(old('_supplier_id') === 'create')
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control"
                                value="{{ old('_supplier_id') === 'create' ? old('contact_person') : '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No HP / Telp</label>
                            <input type="text" name="phone" class="form-control @if(old('_supplier_id') === 'create') @error('phone') is-invalid @enderror @endif"
                                value="{{ old('_supplier_id') === 'create' ? old('phone') : '' }}" required>
                            @if(old('_supplier_id') === 'create')
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" class="form-control" rows="3">{{ old('_supplier_id') === 'create' ? old('address') : '' }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($errors->any())
            @if(old('_supplier_id') === 'create')
                new bootstrap.Modal(document.getElementById('createSupplierModal')).show();
            @elseif(old('_supplier_id'))
                new bootstrap.Modal(document.getElementById('editSupplierModal{{ old("_supplier_id") }}')).show();
            @endif
        @endif
    });
</script>
@endpush