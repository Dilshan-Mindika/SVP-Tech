<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'designation',
        'salary_amount',
        'phone',
        'email',
        'joining_date',
        'status'
    ];

    protected $casts = [
        'salary_amount' => 'float',
        'joining_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function repairs()
    {
        return $this->hasMany(Repair::class, 'assigned_technician_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
