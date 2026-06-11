<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReturnItem extends Model
{
    protected $table = 'return_items';

    protected $fillable = [
        'return_id',
        'product_id',
        'quantity',
        'unit_price'
    ];

    protected $casts = [
        'unit_price' => 'float',
        'quantity' => 'integer'
    ];

    public function productReturn()
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
