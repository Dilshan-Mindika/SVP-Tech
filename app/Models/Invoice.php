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

        // Additional fields
        'invoice_number',
        'customer_id',
        'user_id',
        'employee_id',
        'bank_account_id',
        'repair_id',
        'title',
        'sale_type',
        'special_note',
        'due_date',
        'is_tax_invoice',
        'subtotal',
        'tax',
        'discount',
        'global_discount_percentage',
        'global_discount_amount',
        'service_charges',
        'total',
        'payment_method',
        'is_paid',
        'customer_paid',
        'balance'
    ];

    protected $casts = [
        'is_tax_invoice' => 'boolean',
        'is_paid' => 'boolean',
        'due_date' => 'date',
        'subtotal' => 'float',
        'tax' => 'float',
        'discount' => 'float',
        'global_discount_percentage' => 'float',
        'global_discount_amount' => 'float',
        'service_charges' => 'float',
        'customer_paid' => 'float',
        'balance' => 'float',
        'total' => 'float',
        'bank_account_id' => 'integer',
        'repair_id' => 'integer'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }

    public function returns()
    {
        return $this->hasMany(ProductReturn::class);
    }

    public function warrantyClaims()
    {
        return $this->hasMany(WarrantyClaim::class);
    }

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
