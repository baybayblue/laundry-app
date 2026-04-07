<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['per_kg', 'per_pcs', 'flat'])->default('per_kg');
            $table->decimal('price', 12, 2);
            $table->integer('estimated_hours')->default(24);
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6f42c1');
            $table->string('icon', 50)->default('ti-wash');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
