<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointment';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'appointment_no',
        'customer_name',
        'customer_phone',
        'customer_email',
        'appointment_time',
        'reason',
        'status'
    ];

    protected $casts = [
        'appointment_time' => 'datetime'
    ];
}
