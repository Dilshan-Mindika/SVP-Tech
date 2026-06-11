<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'subtotal',
        'tax',
        'total',
        'valid_until',
        'status',
        'notes'
    ];

    protected $casts = [
        'subtotal' => 'float',
        'tax' => 'float',
        'total' => 'float',
        'valid_until' => 'date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
