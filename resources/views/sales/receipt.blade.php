<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $sale->invoice_number }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; background: #f0f0f0; padding: 20px; }
        .receipt { width: 80mm; background: #fff; padding: 10px; margin: 0 auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { padding: 2px 0; font-size: 12px; }
        .table-bordered-top { border-top: 1px dashed #000; padding-top: 10px; margin-top: 10px; }
        .table-bordered-bottom { border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
        
        @media print {
            body { background: #fff; padding: 0; }
            .receipt { box-shadow: none; width: 100%; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print text-center mb-3">
        <button onclick="window.print()" class="btn btn-primary">Cetak Struk</button>
        <a href="{{ route('sales.create') }}" class="btn btn-success">Transaksi Baru</a>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Dashboard</a>
    </div>

    <div class="receipt">
        <div class="text-center">
            <h3 class="mb-0">{{ \App\Models\Setting::get('store_name', 'Toko POS') }}</h3>
            <p style="font-size: 12px; margin: 2px 0;">{{ \App\Models\Setting::get('store_address', 'Alamat Toko') }}</p>
            <p style="font-size: 12px; margin: 2px 0;">Telp: {{ \App\Models\Setting::get('store_phone', '-') }}</p>
        </div>

        <div class="table-bordered-top" style="font-size: 12px;">
            <div>No: {{ $sale->invoice_number }}</div>
            <div>Tgl: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') }}</div>
            <div>Kasir: {{ $sale->user->name }}</div>
            <div>Pelanggan: {{ $sale->customer ? $sale->customer->name : 'Umum' }}</div>
        </div>

        <table class="table table-bordered-bottom">
            @foreach($sale->items as $item)
                <tr>
                    <td colspan="2">{{ $item->product_name }}</td>
                </tr>
                <tr>
                    <td>{{ $item->quantity }} x @rupiah($item->price)</td>
                    <td class="text-right">@rupiah($item->subtotal)</td>
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

        <div class="text-center" style="font-size: 12px; margin-top: 15px;">
            <p>{{ \App\Models\Setting::get('receipt_footer', 'Terima kasih telah berbelanja!') }}</p>
        </div>
    </div>

</body>
</html>