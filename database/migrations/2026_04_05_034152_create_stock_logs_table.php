<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // admin/karyawan yg input
            $table->enum('type', ['in', 'out']);             // Jenis: masuk atau keluar
            $table->integer('quantity');                      // Jumlah perubahan stok
            $table->integer('stock_before');                  // Stok sebelum perubahan
            $table->integer('stock_after');                   // Stok sesudah perubahan
            $table->string('note')->nullable();              // Keterangan (misal: "Restok dari Supplier A")
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
