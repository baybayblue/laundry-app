<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL/MariaDB, changing an ENUM is best done with a raw query
        // to avoid issues with doctrine/dbal or native change() on ENUMs.
        DB::statement("ALTER TABLE transactions MODIFY COLUMN order_status ENUM('pending', 'processing', 'done', 'delivered', 'cancelled', 'cancel_requested') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum list. Note: records with 'cancel_requested' might cause issues if not changed first.
        DB::statement("ALTER TABLE transactions MODIFY COLUMN order_status ENUM('pending', 'processing', 'done', 'delivered', 'cancelled') DEFAULT 'pending'");
    }
};
