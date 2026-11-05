<?php

namespace App\Support;

use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;

class DocumentNumber
{
    public static function generatePurchaseNumber()
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = 'PB-' . $date . '-';
        
        // Cari nomor terakhir hari ini
        $lastPurchase = Purchase::where('purchase_number', 'like', $prefix . '%')
                                ->orderBy('purchase_number', 'desc')
                                ->first();

        if (!$lastPurchase) {
            $number = '0001';
        } else {
            $lastNumber = substr($lastPurchase->purchase_number, -4);
            $number = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . $number;
    }

    public static function generateInvoiceNumber()
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = 'INV-' . $date . '-';
        
        $lastSale = Sale::where('invoice_number', 'like', $prefix . '%')
                        ->orderBy('invoice_number', 'desc')
                        ->first();

        if (!$lastSale) {
            $number = '0001';
        } else {
            $lastNumber = substr($lastSale->invoice_number, -4);
            $number = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . $number;
    }
}