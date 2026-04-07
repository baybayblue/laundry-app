<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'color', 'icon'];

    public function stockItems()
    {
        return $this->hasMany(StockItem::class);
    }
}
