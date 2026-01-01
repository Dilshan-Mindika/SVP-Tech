<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'brand',
        'model',
        'stock_quantity',
        'low_stock_threshold',
        'cost_price',
        'selling_price',
    ];

    // Helper to check stock status
    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) return 'Out of Stock';
        if ($this->stock_quantity <= $this->low_stock_threshold) return 'Low Stock';
        return 'In Stock';
    }
}
