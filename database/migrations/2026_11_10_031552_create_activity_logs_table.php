<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50); // Contoh: create, update, delete
            $table->string('table_name', 50); // Nama tabel yang diubah (misal: products)
            $table->unsignedBigInteger('record_id'); // ID dari data yang diubah
            $table->text('old_data')->nullable(); // Data sebelum diubah (disimpan dalam format JSON)
            $table->text('new_data')->nullable(); // Data sesudah diubah (disimpan dalam format JSON)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};