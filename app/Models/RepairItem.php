<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairItem extends Model
{
    protected $fillable = [
        'repair_id',
        'product_id',
        'quantity',
        'price'
    ];

    protected $casts = [
        'price' => 'float',
        'quantity' => 'integer'
    ];

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
