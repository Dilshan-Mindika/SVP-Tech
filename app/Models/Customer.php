<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected static function booted()
    {
        static::addGlobalScope('role', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where('role', 'USER');
        });

        static::creating(function ($customer) {
            $customer->role = 'USER';
            if (empty($customer->password)) {
                $customer->password = bcrypt('password');
            }
        });
    }

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'type',
        'credit_balance',
    ];

    public function repairJobs()
    {
        return $this->hasMany(RepairJob::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, RepairJob::class);
    }

    public function getTotalDueAttribute()
    {
        return $this->invoices->where('status', '!=', 'paid')->sum('balance_due');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
