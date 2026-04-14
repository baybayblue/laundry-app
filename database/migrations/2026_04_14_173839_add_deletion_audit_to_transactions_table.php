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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('delete_requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('delete_reason')->nullable();
            $table->timestamp('delete_requested_at')->nullable();
            $table->foreignId('delete_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('delete_approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['delete_requested_by']);
            $table->dropForeign(['delete_approved_by']);
            $table->dropColumn(['delete_requested_by', 'delete_reason', 'delete_requested_at', 'delete_approved_by', 'delete_approved_at']);
        });
    }
};
