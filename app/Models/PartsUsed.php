<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartsUsed extends Model
{
    use HasFactory;

    protected $table = 'parts_used'; // Explicitly define table name since it's plural

    protected $fillable = [
        'repair_job_id',
        'supplier_id',
        'part_name',
        'part_cost',
        'quantity_used',
    ];

    public function repairJob()
    {
        return $this->belongsTo(RepairJob::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
