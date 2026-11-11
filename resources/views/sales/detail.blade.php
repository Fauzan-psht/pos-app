@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Detail Transaksi: {{ $sale->invoice_number }}</h4>
        <div>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Kembali ke Riwayat</a>
            <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-primary" target="_blank"><i class="bi bi-printer"></i> Cetak Struk</a>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Info Transaksi -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3">Informasi Transaksi</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr><td class="text-muted" width="120">No Invoice</td><td class="fw-bold">{{ $sale->invoice_number }}</td></tr>
                        <tr><td class="text-muted">Tanggal</td><td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</td></tr>
                        <tr><td class="text-muted">Kasir</td><td>{{ $sale->user->name }}</td></tr>
                        <tr><td class="text-muted">Pelanggan</td><td>{{ $sale->customer ? $sale->customer->name : 'Umum' }}</td></tr>
                        <tr><td class="text-muted">Metode Bayar</td><td><span class="badge bg-secondary">{{ strtoupper($sale->payment_method) }}</span></td></tr>
                        <tr><td class="text-muted">Status</td>
                            <td>
                                @if($sale->status === 'paid')
                                    <span class="badge bg-success">Sukses</span>
                                @else
                                    <span class="badge bg-danger">Dibatalkan</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                    
                    {{-- TOMBOL VOID (B2) HANYA UNTUK ADMIN DAN JIKA STATUS PAID --}}
                    @if(auth()->user()->role === 'admin' && $sale->status === 'paid')
                        <hr>
                        <button type="button" class="btn btn-danger w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#voidModal">
                            <i class="bi bi-x-circle"></i> Batalkan Transaksi Ini
                        </button>
                    @endif

                    @if($sale->status === 'void')
                        <div class="alert alert-danger mt-3 mb-0" style="font-size: 13px;">
                            <strong>Alasan Dibatalkan:</strong><br>
                            {{ $sale->note }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Rincian Barang -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-bold py-3">Rincian Barang</div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Produk</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $item->product_name }}</div>
                                    </td>
                                    <td>@rupiah($item->price)</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end pe-4 fw-bold">@rupiah($item->subtotal)</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end">Subtotal</td>
                                <td class="text-end pe-4">@rupiah($sale->subtotal)</td>
                            </tr>
                            @if($sale->discount > 0)
                            <tr>
                                <td colspan="3" class="text-end">Diskon</td>
                                <td class="text-end pe-4">-@rupiah($sale->discount)</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="text-end fw-bold fs-5">TOTAL</td>
                                <td class="text-end pe-4 fw-bold fs-5 text-primary">@rupiah($sale->total)</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL VOID (B2) -->
    @if(auth()->user()->role === 'admin' && $sale->status === 'paid')
    <div class="modal fade" id="voidModal" tabindex="-1" aria-labelledby="voidModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="voidModalLabel">Konfirmasi Pembatalan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('sales.void', $sale->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin membatalkan transaksi <strong>{{ $sale->invoice_number }}</strong>?</p>
                        <p class="text-danger"><small>Stok akan dikembalikan otomatis ke gudang.</small></p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Pembatalan</label>
                            <textarea name="void_reason" class="form-control" rows="3" required placeholder="Contoh: Pelanggan salah input barang, atau batal beli."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-danger">Ya, Batalkan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection