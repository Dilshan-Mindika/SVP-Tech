<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    // Use returns table
    protected $table = 'returns';

    protected $fillable = [
        'return_number',
        'invoice_id',
        'supplier_id',
        'type',
        'reason',
        'refund_amount',
        'status'
    ];

    protected $casts = [
        'refund_amount' => 'float'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(ProductReturnItem::class, 'return_id');
    }
}
