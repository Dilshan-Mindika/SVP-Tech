<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_job_id',
        'description',
        'quantity',
        'quantity',
        'amount',
        'unit_cost',
    ];

    public function repairJob()
    {
        return $this->belongsTo(RepairJob::class);
    }
}
