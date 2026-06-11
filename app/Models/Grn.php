<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    protected $fillable = [
        'grn_number',
        'supplier_id',
        'received_by',
        'date_received',
        'subtotal',
        'discount_percentage',
        'discount_amount',
        'service_charges',
        'total_amount',
        'payment_type',
        'is_paid',
        'paid_amount',
        'notes'
    ];

    protected $casts = [
        'date_received' => 'date',
        'subtotal' => 'float',
        'discount_percentage' => 'float',
        'discount_amount' => 'float',
        'service_charges' => 'float',
        'total_amount' => 'float',
        'is_paid' => 'boolean',
        'paid_amount' => 'float'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items()
    {
        return $this->hasMany(GrnItem::class);
    }
}
