<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyClaim extends Model
{
    protected $fillable = [
        'claim_number',
        'invoice_id',
        'product_id',
        'serial_number',
        'customer_id',
        'claim_date',
        'issue_description',
        'status',
        'action_taken',
        'closed_date'
    ];

    protected $casts = [
        'claim_date' => 'date',
        'closed_date' => 'date'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
