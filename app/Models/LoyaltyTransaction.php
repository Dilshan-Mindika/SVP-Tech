<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'points',
        'transaction_type',
        'description'
    ];

    protected $casts = [
        'points' => 'integer'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
