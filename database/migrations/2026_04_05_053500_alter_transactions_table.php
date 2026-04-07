<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('invoice_number')->unique()->after('id');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->after('invoice_number');
            $table->string('customer_name')->after('customer_id');           // walk-in or snapshot
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->foreignId('discount_id')->nullable()->constrained('discounts')->nullOnDelete()->after('customer_phone');
            $table->string('discount_code')->nullable()->after('discount_id');
            $table->decimal('subtotal', 12, 2)->default(0)->after('discount_code');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('subtotal');
            $table->decimal('tax_amount', 12, 2)->default(0)->after('discount_amount');
            $table->decimal('service_fee', 12, 2)->default(0)->after('tax_amount');
            $table->decimal('total_amount', 12, 2)->default(0)->after('service_fee');
            $table->enum('payment_method', ['cash', 'midtrans'])->default('cash')->after('total_amount');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired'])->default('pending')->after('payment_method');
            $table->enum('order_status', ['pending', 'processing', 'done', 'delivered', 'cancelled'])->default('pending')->after('payment_status');
            $table->text('notes')->nullable()->after('order_status');
            $table->date('pickup_date')->nullable()->after('notes');
            $table->string('midtrans_order_id')->nullable()->unique()->after('pickup_date');
            $table->text('midtrans_snap_token')->nullable()->after('midtrans_order_id');
            $table->string('midtrans_payment_type')->nullable()->after('midtrans_snap_token');
            $table->timestamp('paid_at')->nullable()->after('midtrans_payment_type');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['discount_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'invoice_number', 'customer_id', 'customer_name', 'customer_phone',
                'discount_id', 'discount_code', 'subtotal', 'discount_amount', 'tax_amount',
                'service_fee', 'total_amount', 'payment_method', 'payment_status',
                'order_status', 'notes', 'pickup_date', 'midtrans_order_id',
                'midtrans_snap_token', 'midtrans_payment_type', 'paid_at', 'created_by',
            ]);
        });
    }
};
