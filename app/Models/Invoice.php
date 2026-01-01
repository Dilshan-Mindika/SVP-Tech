<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'repair_job_id',
        'invoice_type',
        'total_amount',
        'parts_cost',
        'labor_cost',
        'profit_margin',
    ];

    public function repairJob()
    {
        return $this->belongsTo(RepairJob::class);
    }
}
