<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_id', 'customer_name', 'customer_phone',
        'discount_id', 'discount_code', 'subtotal', 'discount_amount',
        'tax_amount', 'service_fee', 'total_amount',
        'payment_method', 'payment_status', 'order_status', 'notes',
        'pickup_date', 'midtrans_order_id', 'midtrans_snap_token',
        'midtrans_payment_type', 'paid_at', 'created_by',
        'delete_requested_by', 'delete_reason', 'delete_requested_at',
        'delete_approved_by', 'delete_approved_at',
        'source', 'pickup_address',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'service_fee'     => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'pickup_date'     => 'date',
        'paid_at'             => 'datetime',
        'delete_requested_at' => 'datetime',
        'delete_approved_at'  => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deleteRequestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delete_requested_by');
    }

    public function deleteApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delete_approved_by');
    }

    // ── Computed Attributes ────────────────────────────────────
    public function getOrderStatusLabelAttribute(): string
    {
        return match ($this->order_status) {
            'pending'          => 'Menunggu',
            'processing'       => 'Diproses',
            'done'             => 'Selesai',
            'delivered'        => 'Terkirim',
            'cancelled'        => 'Dibatalkan',
            'cancel_requested' => 'Menunggu Penghapusan',
            default            => ucfirst($this->order_status),
        };
    }

    public function getOrderStatusColorAttribute(): string
    {
        return match ($this->order_status) {
            'pending'          => '#fd7e14',
            'processing'       => '#0d6efd',
            'done'             => '#198754',
            'delivered'        => '#20c997',
            'cancelled'        => '#dc3545',
            'cancel_requested' => '#6f42c1',
            default            => '#6c757d',
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'Belum Bayar',
            'paid'    => 'Lunas',
            'failed'  => 'Gagal',
            'expired' => 'Kadaluarsa',
            default   => ucfirst($this->payment_status),
        };
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'    => '#198754',
            'pending' => '#fd7e14',
            'failed'  => '#dc3545',
            'expired' => '#6c757d',
            default   => '#6c757d',
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'walk_in'          => 'Walk-in',
            'online_customer'  => 'Online (Pelanggan)',
            default            => ucfirst($this->source),
        };
    }

    public function getSourceColorAttribute(): string
    {
        return match ($this->source) {
            'walk_in'          => '#6c757d',
            'online_customer'  => '#2563eb',
            default            => '#6c757d',
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_amount, 0, ',', '.');
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash'     => 'Tunai',
            'midtrans' => 'Online (Midtrans)',
            default    => ucfirst($this->payment_method),
        };
    }

    public function getIsEditableAttribute(): bool
    {
        return !($this->payment_status === 'paid' && $this->order_status === 'delivered');
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->count();
    }

    // ── Static: Generate Invoice Number ───────────────────────
    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "TRX-{$date}-";

        $last = static::where('invoice_number', 'like', "{$prefix}%")
            ->orderByDesc('id')
            ->value('invoice_number');

        $seq = $last
            ? (int) substr($last, -4) + 1
            : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopeByOrderStatus($q, string $status)
    {
        return $q->where('order_status', $status);
    }

    public function scopeByPaymentStatus($q, string $status)
    {
        return $q->where('payment_status', $status);
    }

    public function scopeSearch($q, string $term)
    {
        return $q->where(function ($query) use ($term) {
            $query->where('invoice_number', 'like', "%{$term}%")
                  ->orWhere('customer_name', 'like', "%{$term}%")
                  ->orWhere('customer_phone', 'like', "%{$term}%");
        });
    }
}
