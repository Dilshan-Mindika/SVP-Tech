<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class RepairJob extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'technician_id',
        'job_number',
        'laptop_brand',
        'laptop_model',
        'serial_number',
        'fault_description',
        'repair_status',
        'repair_notes',
        'parts_used_cost',
        'labor_cost',
        'final_price',
        'job_invoice_generated_at',
        'service_invoice_generated_at',
        'completed_at',
        'delivered_at',
    ];

    protected $casts = [
        'job_invoice_generated_at' => 'datetime',
        'service_invoice_generated_at' => 'datetime',
        'completed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function parts()
    {
        return $this->hasMany(PartsUsed::class); // Keep for backward compatibility if needed, or deprecate
    }

    public function expenses()
    {
        return $this->hasMany(JobExpense::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(JobInvoiceItem::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
