<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'employee_id',
        'amount_paid',
        'paid_for_month',
        'payment_date',
        'payment_method',
        'payslip_no'
    ];

    protected $casts = [
        'amount_paid' => 'float',
        'payment_date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
