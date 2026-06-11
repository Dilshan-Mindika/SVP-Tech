<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'expense_no',
        'category',
        'amount',
        'date_incurred',
        'payment_method',
        'details'
    ];

    protected $casts = [
        'amount' => 'float',
        'date_incurred' => 'date'
    ];
}
