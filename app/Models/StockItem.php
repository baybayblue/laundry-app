<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'unit',
        'stock',
        'min_stock',
        'price_per_unit',
        'supplier',
        'description',
        'photo',
    ];

    protected $casts = [
        'price_per_unit' => 'decimal:2',
    ];

    public function logs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Apakah stok sudah mencapai batas minimum?
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Status stok sebagai label
     */
    public function stockStatus(): string
    {
        if ($this->stock === 0) return 'habis';
        if ($this->isLowStock()) return 'menipis';
        return 'aman';
    }
}
