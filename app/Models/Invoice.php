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
        'paid_amount',
        'status',
    ];

    public function repairJob()
    {
        return $this->belongsTo(RepairJob::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceDueAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function recalculateStatus()
    {
        $totalPaid = $this->payments()->sum('amount');
        $this->paid_amount = $totalPaid;

        if ($totalPaid >= $this->total_amount) {
            $this->status = 'paid';
        } elseif ($totalPaid > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'unpaid';
        }

        $this->save();

        // Sync with Repair Job
        if ($this->repairJob) {
            $this->repairJob->update(['payment_status' => $this->status]);
        }
    }
}
