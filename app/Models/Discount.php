<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'min_transaction',
        'max_discount',
        'usage_limit',
        'usage_count',
        'start_date',
        'end_date',
        'is_active',
        'description',
    ];

    protected $casts = [
        'value'           => 'decimal:2',
        'min_transaction' => 'decimal:2',
        'max_discount'    => 'decimal:2',
        'start_date'      => 'date',
        'end_date'        => 'date',
        'is_active'       => 'boolean',
    ];

    // ── Scopes ──────────────────────────────────────────
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()));
    }

    // ── Status helpers ────────────────────────────────
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) return 'inactive';
        if ($this->start_date && $this->start_date->isFuture()) return 'upcoming';
        if ($this->end_date   && $this->end_date->isPast())   return 'expired';
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) return 'exhausted';
        return 'active';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'Aktif',
            'inactive'  => 'Nonaktif',
            'upcoming'  => 'Akan Datang',
            'expired'   => 'Kadaluarsa',
            'exhausted' => 'Habis',
            default     => '-',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'success',
            'inactive'  => 'secondary',
            'upcoming'  => 'info',
            'expired'   => 'danger',
            'exhausted' => 'warning',
            default     => 'secondary',
        };
    }

    public function getFormattedValueAttribute(): string
    {
        return $this->type === 'percentage'
            ? number_format($this->value, 0) . '%'
            : 'Rp ' . number_format($this->value, 0, ',', '.');
    }

    public function isUsable(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Hitung potongan untuk nominal transaksi tertentu
     */
    public function calculate(float $amount): float
    {
        if ($this->min_transaction && $amount < $this->min_transaction) {
            return 0;
        }

        if ($this->type === 'fixed') {
            return min($this->value, $amount);
        }

        // percentage
        $discount = $amount * ($this->value / 100);
        if ($this->max_discount) {
            $discount = min($discount, $this->max_discount);
        }
        return $discount;
    }
}
