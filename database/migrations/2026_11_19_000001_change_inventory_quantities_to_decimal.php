<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const QUANTITY_COLUMNS = [
        'products' => ['stock', 'min_stock'],
        'sale_items' => ['quantity'],
        'purchase_items' => ['quantity'],
    ];

    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock', 15, 3)->default(0)->change();
            $table->decimal('min_stock', 15, 3)->default(0)->change();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('quantity', 15, 3)->change();
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->decimal('quantity', 15, 3)->change();
        });
    }

    public function down(): void
    {
        foreach (self::QUANTITY_COLUMNS as $table => $columns) {
            foreach ($columns as $column) {
                if (DB::table($table)->whereRaw("`{$column}` <> ROUND(`{$column}`, 0)")->exists()) {
                    throw new RuntimeException('Cannot roll back inventory quantities while fractional values exist.');
                }
            }
        }

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->change();
            $table->unsignedInteger('min_stock')->default(0)->change();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->change();
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->change();
        });
    }
};
