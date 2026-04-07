<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('service_name');                           // snapshot nama layanan
            $table->string('service_type');                           // per_kg / per_pcs / flat
            $table->decimal('quantity', 8, 2)->default(1);           // bisa 2.5 kg
            $table->decimal('unit_price', 12, 2)->default(0);        // harga snapshot
            $table->decimal('subtotal', 12, 2)->default(0);          // qty * price
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
