<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'bank_name',
        'account_name',
        'account_number',
        'branch',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
