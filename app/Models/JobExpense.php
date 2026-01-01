<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_job_id',
        'description',
        'amount',
    ];

    public function repairJob()
    {
        return $this->belongsTo(RepairJob::class);
    }
}
