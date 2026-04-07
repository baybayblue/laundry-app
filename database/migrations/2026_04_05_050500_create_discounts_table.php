<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                         // Nama diskon: "Promo Lebaran", "Member"
            $table->string('code')->unique()->nullable();                   // Kode kupon (opsional)
            $table->enum('type', ['percentage', 'fixed'])->default('percentage'); // % atau nominal Rp
            $table->decimal('value', 12, 2);                               // Nilai diskon
            $table->decimal('min_transaction', 12, 2)->nullable();         // Min. transaksi (opsional)
            $table->decimal('max_discount', 12, 2)->nullable();            // Maks. potongan untuk tipe %
            $table->integer('usage_limit')->nullable();                    // Maks. penggunaan total (null = ∞)
            $table->integer('usage_count')->default(0);                    // Hitungan penggunaan
            $table->date('start_date')->nullable();                        // Berlaku mulai
            $table->date('end_date')->nullable();                          // Berlaku sampai
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
