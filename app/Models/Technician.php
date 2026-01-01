<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Technician extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'specialty',
        'total_jobs',
        'average_time_per_job',
        'performance_score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function repairJobs()
    {
        return $this->hasMany(RepairJob::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
