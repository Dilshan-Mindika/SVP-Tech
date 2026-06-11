<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'free_quantity',
        'unit_price',
        'discount_amount',
        'discount_percentage',
        'total',
        'serial_number',
        'warranty'
    ];

    protected $casts = [
        'unit_price' => 'float',
        'discount_amount' => 'float',
        'discount_percentage' => 'float',
        'total' => 'float',
        'quantity' => 'integer',
        'free_quantity' => 'integer'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
