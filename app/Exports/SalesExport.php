<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection(): Collection
    {
        return Sale::with(['user', 'customer'])
            ->where('status', 'paid')
            ->whereBetween('sale_date', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'No Invoice',
            'Tanggal',
            'Kasir',
            'Pelanggan',
            'Metode Bayar',
            'Subtotal',
            'Diskon',
            'Total',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->invoice_number,
            \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i'),
            $sale->user->name,
            $sale->customer ? $sale->customer->name : 'Umum',
            strtoupper($sale->payment_method),
            $sale->subtotal,
            $sale->discount,
            $sale->total,
        ];
    }

    // FORMAT KOLOM UANG (Gunakan format standar Excel)
    public function columnFormats(): array
    {
        return [
            'F' => '#,##0', // Subtotal (pemisah ribuan)
            'G' => '#,##0', // Diskon
            'H' => '#,##0', // Total
        ];
    }
}