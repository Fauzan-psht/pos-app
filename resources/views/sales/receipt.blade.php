<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $sale->invoice_number }}</title>
          <style>
        /* === TAMPILAN DI LAYAR WEB === */
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
        
        /* === TAMPILAN SAAT DICETAK PDF === */
        @media print {
            /* Kertas tetap A4 jika tidak diubah manual, tapi struk akan di tengah */
            body { 
                background: #fff; 
                padding: 0; 
                margin: 0;
            }
            .receipt-container {
                display: flex; /* Tetap pakai flex agar struk di tengah */
                justify-content: center; /* Menengahkan struk */
                margin-top: 50px; /* Memberi jarak dari atas kertas */
            }
            .receipt { 
                box-shadow: none; 
                width: 80mm; 
                padding: 10px; 
                margin: 0;
            }
            .no-print { 
                display: none !important; 
            }
        }
    </style>
</head>
<body>

    <!-- Tombol Navigasi (Hanya muncul di layar) -->
    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background-color: #0d6efd; color: white; border: none; border-radius: 5px;">
            🖨️ Cetak Struk
        </button>
        <br><br>
        <a href="{{ route('sales.create') }}" style="color: #198754; text-decoration: none; font-weight: bold; margin-right: 15px;">+ Transaksi Baru</a>
        <a href="{{ route('dashboard') }}" style="color: #6c757d; text-decoration: none;">Kembali ke Dashboard</a>
    </div>

    <!-- Wadah untuk menengahkan struk -->
    <div class="receipt-container">
        <div class="receipt">
            <!-- ... (ISI STRUK DARI <div class="text-center"> SAMPAI FOOTER TETAP SAMA SEPERTI SEBELUMNYA) ... -->
            
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

</body>
</html>