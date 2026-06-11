<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnItem extends Model
{
    protected $fillable = [
        'grn_id',
        'product_id',
        'quantity',
        'free_quantity',
        'buying_price',
        'wholesale_price',
        'barcode',
        'expire_date',
        'discount_percentage',
        'discount_amount',
        'single_discount_amount',
        'warranty_months'
    ];

    protected $casts = [
        'buying_price' => 'float',
        'wholesale_price' => 'float',
        'quantity' => 'integer',
        'free_quantity' => 'integer',
        'discount_percentage' => 'float',
        'discount_amount' => 'float',
        'single_discount_amount' => 'float',
        'warranty_months' => 'integer',
        'expire_date' => 'date'
    ];

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
