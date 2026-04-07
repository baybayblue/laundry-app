<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Service extends Model
{
    protected $fillable = [
        'name',
        'type',
        'price',
        'estimated_hours',
        'description',
        'color',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'price'      => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'per_kg'  => 'per Kg',
            'per_pcs' => 'per Pcs',
            'flat'    => 'Flat',
            default   => $this->type,
        };
    }

    public function getFormattedPrice(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getEstimatedLabel(): string
    {
        $h = $this->estimated_hours;
        if ($h < 24) return $h . ' jam';
        $d = intdiv($h, 24);
        $r = $h % 24;
        return $d . ' hari' . ($r > 0 ? ' ' . $r . ' jam' : '');
    }
}
