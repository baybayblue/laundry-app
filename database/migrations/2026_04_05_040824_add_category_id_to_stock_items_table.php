<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add FK constraint now that categories table exists
        if (Schema::hasColumn('stock_items', 'category_id') &&
            !$this->hasFk('stock_items', 'category_id')) {
            Schema::table('stock_items', function (Blueprint $table) {
                $table->foreign('category_id')
                      ->references('id')
                      ->on('categories')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            try { $table->dropForeign(['category_id']); } catch (\Throwable $e) {}
        });
    }

    private function hasFk(string $table, string $column): bool
    {
        try {
            $fks = \Illuminate\Support\Facades\DB::select(
                "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL
                 AND TABLE_SCHEMA = DATABASE()",
                [$table, $column]
            );
            return count($fks) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
};
