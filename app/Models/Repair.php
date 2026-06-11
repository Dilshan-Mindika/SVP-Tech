<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    protected $table = 'repair';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'repair_job_no',
        'customer_name',
        'customer_phone',
        'customer_email',
        'device_model',
        'device_serial',
        'issue_description',
        'estimate_cost',
        'final_cost',
        'assigned_technician_id',
        'status',
        'notes',

        // New fields
        'customer_whatsapp',
        'customer_address',
        'customer_nic',
        'customer_company',
        'referred_by',
        'device_brand',
        'device_color',
        'device_processor',
        'device_storage',
        'device_ram',
        'device_display_size',
        'device_battery',
        'device_charger_watt',
        'physical_condition',
        'physical_condition_other',
        'accessories_received',
        'accessories_other',
        'windows_password',
        'bios_password',
        'bitlocker_status',
        'data_backup_required',
        'customer_accept_data_loss',
        'technical_inspection',
        'chip_level_repair_notes',
        'board_model',
        'freelancer_technician',
        'sent_date',
        'return_date',
        'inspection_fee',
        'advance_payment',
        'balance',
        'collected_by',
        'date_collected',
        'remaining_balance_paid'
    ];

    protected $casts = [
        'estimate_cost' => 'float',
        'final_cost' => 'float',
        'physical_condition' => 'array',
        'accessories_received' => 'array',
        'technical_inspection' => 'array',
        'chip_level_repair_notes' => 'array',
        'data_backup_required' => 'boolean',
        'customer_accept_data_loss' => 'boolean',
        'inspection_fee' => 'float',
        'advance_payment' => 'float',
        'balance' => 'float',
        'remaining_balance_paid' => 'float',
        'sent_date' => 'date',
        'return_date' => 'date',
        'date_collected' => 'date'
    ];

    public function technician()
    {
        return $this->belongsTo(Employee::class, 'assigned_technician_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getRepairJobNoAttribute($value)
    {
        return $value ?: $this->job_number;
    }

    public function getCustomerNameAttribute($value)
    {
        if ($value) return $value;
        return $this->customer ? $this->customer->name : 'Walk-in';
    }

    public function getCustomerPhoneAttribute($value)
    {
        if ($value) return $value;
        return $this->customer ? $this->customer->phone : null;
    }

    public function getDeviceModelAttribute($value)
    {
        if ($value) return $value;
        return $this->laptop_model ?: ($this->laptop_brand ?: 'Unknown Device');
    }

    public function getStatusAttribute($value)
    {
        $status = $value ?: ($this->repair_status ?: 'received');
        return strtolower($status);
    }
    
    public function getEstimateCostAttribute($value)
    {
        if ($value > 0) return $value;
        return $this->final_price ?: ($this->labor_cost + $this->parts_used_cost ?: 0.0);
    }

    public function items()
    {
        return $this->hasMany(RepairItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
