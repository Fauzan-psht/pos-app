<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $purchase = rand(5000, 50000);
            Product::create([
                'category_id' => rand(1, 5),
                'sku' => 'PRD-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Produk Contoh ' . $i,
                'unit' => 'Pcs',
                'purchase_price' => $purchase,
                'selling_price' => $purchase + 5000,
                'stock' => rand(0, 50),
                'min_stock' => 10,
                'is_active' => true,
            ]);
        }
    }
}