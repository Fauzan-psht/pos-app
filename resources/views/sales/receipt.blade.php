<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $sale->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { 
            font-family: 'Courier New', Courier, monospace; 
            background: #f0f0f0; 
            padding: 20px; 
            margin: 0;
        }
        .receipt-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .receipt { 
            width: 80mm; 
            background: #fff; 
            padding: 15px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { padding: 2px 0; font-size: 12px; }
        .table-bordered-top { border-top: 1px dashed #000; padding-top: 10px; margin-top: 10px; }
        .table-bordered-bottom { border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
        
        @media print {
            body { background: #fff; padding: 0; margin: 0; }
            .receipt-container { display: flex; justify-content: center; margin-top: 50px; }
            .receipt { box-shadow: none; width: 80mm; padding: 10px; margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <!-- Tombol Navigasi & Void (Hanya muncul di layar) -->
    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background-color: #0d6efd; color: white; border: none; border-radius: 5px;">
            🖨️ Cetak Struk
        </button>
        <br><br>
        <a href="{{ route('sales.create') }}" style="color: #198754; text-decoration: none; font-weight: bold; margin-right: 15px;">+ Transaksi Baru</a>
        
        <!-- TOMBOL VOID KHUSUS ADMIN -->
        @if(auth()->user()->role === 'admin' && $sale->status === 'paid')
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#voidModal" style="margin-right: 15px;">
                <i class="bi bi-x-circle"></i> Void Transaksi
            </button>
        @endif

        <a href="{{ route('dashboard') }}" style="color: #6c757d; text-decoration: none;">Kembali ke Dashboard</a>
    </div>

    <!-- Wadah untuk menengahkan struk -->
    <div class="receipt-container">
        <div class="receipt">
            
            <!-- TANDA VOID DI STRUK -->
            @if($sale->status === 'void')
                <div class="text-center" style="border: 2px dashed red; padding: 5px; margin-bottom: 15px;">
                    <h4 style="color: red; margin: 0; font-weight: bold;">*** VOID ***</h4>
                    <p style="font-size: 10px; margin: 5px 0 0 0; color: red;">Alasan: {{ $sale->note }}</p>
                </div>
            @endif

            <div class="text-center">
                <h3 class="mb-0" style="margin: 0 0 5px 0; font-size: 16px;">{{ \App\Models\Setting::get('store_name', 'Toko POS') }}</h3>
                <p style="font-size: 11px; margin: 2px 0;">{{ \App\Models\Setting::get('store_address', 'Alamat Toko') }}</p>
                <p style="font-size: 11px; margin: 2px 0;">Telp: {{ \App\Models\Setting::get('store_phone', '-') }}</p>
            </div>

            <div class="table-bordered-top" style="font-size: 11px;">
                <div>No: {{ $sale->invoice_number }}</div>
                <div>Tgl: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</div>
                <div>Kasir: {{ $sale->user->name }}</div>
                <div>Pelanggan: {{ $sale->customer ? $sale->customer->name : 'Umum' }}</div>
            </div>

            <table class="table table-bordered-bottom">
                @foreach($sale->items as $item)
                    <tr>
                        <td colspan="2" style="font-size: 12px;">{{ $item->product_name }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px;">{{ $item->quantity }} x @rupiah($item->price)</td>
                        <td class="text-right" style="font-size: 12px;">@rupiah($item->subtotal)</td>
                    </tr>
                @endforeach
            </table>

            <table class="table">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">@rupiah($sale->subtotal)</td>
                </tr>
                @if($sale->discount > 0)
                <tr>
                    <td>Diskon</td>
                    <td class="text-right">-@rupiah($sale->discount)</td>
                </tr>
                @endif
                <tr style="font-weight: bold; font-size: 14px;">
                    <td>TOTAL</td>
                    <td class="text-right">@rupiah($sale->total)</td>
                </tr>
                <tr>
                    <td>Bayar ({{ strtoupper($sale->payment_method) }})</td>
                    <td class="text-right">@rupiah($sale->paid_amount)</td>
                </tr>
                <tr>
                    <td>Kembali</td>
                    <td class="text-right">@rupiah($sale->change_amount)</td>
                </tr>
            </table>

            <div class="text-center" style="font-size: 11px; margin-top: 15px;">
                <p>{{ \App\Models\Setting::get('receipt_footer', 'Terima kasih telah berbelanja!') }}</p>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI VOID -->
    @if(auth()->user()->role === 'admin' && $sale->status === 'paid')
    <div class="modal fade" id="voidModal" tabindex="-1" aria-labelledby="voidModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="voidModalLabel">Konfirmasi Void Transaksi</h5>
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

    <!-- Bootstrap JS untuk Modal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>