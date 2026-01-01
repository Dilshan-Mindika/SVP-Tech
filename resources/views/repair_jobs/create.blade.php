@extends('layouts.app')

@section('title', 'New Repair Job')

@section('content')
<div class="page-header">
    <h2>Create New Repair Job</h2>
</div>

<div class="card glass" style="max-width: 800px; margin: 0 auto; padding: 2rem;" x-data="{ customerType: 'existing' }">
    <form action="{{ route('repair-jobs.store') }}" method="POST">
        @csrf
        
        <h3 class="section-title">Customer Information</h3>
        
        <!-- Selection Toggle -->
        <div class="form-group">
            <div style="display: flex; gap: 1.5rem; margin-bottom: 1rem;">
                <label style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                    <input type="radio" name="customer_type" value="existing" x-model="customerType">
                    Existing Customer
                </label>
                <label style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                    <input type="radio" name="customer_type" value="new" x-model="customerType">
                    New Customer
                </label>
            </div>
        </div>

        <!-- Existing Customer Select -->
        <div class="form-group" x-show="customerType === 'existing'">
            <label>Select Customer</label>
            <select name="customer_id" class="form-control">
                <option value="">-- Choose Customer --</option>
                @foreach(\App\Models\Customer::orderBy('name')->get() as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                @endforeach
            </select>
        </div>

        <!-- New Customer Fields -->
        <div x-show="customerType === 'new'" style="background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label>Full Name <span class="text-danger">*</span></label>
                <input type="text" name="new_customer_name" placeholder="Enter name">
            </div>
            
            <div class="grid-2">
                <div class="form-group">
                    <label>Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="new_customer_phone" placeholder="Contact number">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="new_customer_email" placeholder="Optional email">
                </div>
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="new_customer_address" placeholder="Shipping/Billing Address">
            </div>
        </div>

        <h3 class="section-title" style="margin-top: 2rem;">Job Details</h3>
        <div class="form-group">
            <label>Job Number (Auto-Generated)</label>
            <input type="text" name="job_number" value="{{ $nextJobNumber }}" class="form-control" style="font-family: monospace; font-size: 1.1rem; letter-spacing: 1px; width: 200px;" required>
        </div>

        <h3 class="section-title" style="margin-top: 2rem;">Device Details</h3>
        <div class="grid-2">
            <div class="form-group">
                <label>Brand</label>
                <input type="text" name="laptop_brand" list="brand_list" required placeholder="Select or type brand..." autocomplete="off">
                <datalist id="brand_list">
                    <option value="Apple">
                    <option value="Dell">
                    <option value="HP">
                    <option value="Lenovo">
                    <option value="Asus">
                    <option value="Acer">
                    <option value="MSI">
                    <option value="Microsoft Surface">
                    <option value="Samsung">
                    <option value="Razer">
                    <option value="Alienware">
                    <option value="Huawei">
                    <option value="LG">
                    <option value="Toshiba">
                    <option value="Sony Vaio">
                    <option value="Fujitsu">
                    <option value="Gigabyte">
                    <option value="Panasonic">
                    <option value="Gateway">
                    <option value="Google Pixelbook">
                </datalist>
            </div>
            <div class="form-group">
                <label>Model</label>
                <input type="text" name="laptop_model" required placeholder="e.g. XPS 15, MacBook Pro M1...">
            </div>
        </div>
        
        <div class="form-group">
            <label>Serial Number</label>
            <input type="text" name="serial_number" placeholder="S/N: 123456789">
        </div>

        <h3 class="section-title" style="margin-top: 2rem;">Fault Diagnosis</h3>
        <div class="form-group">
            <label>Fault Description</label>
            <textarea name="fault_description" class="form-control" rows="4" required placeholder="Describe the issue..."></textarea>
        </div>

        <div class="form-group">
            <label>Initial Notes (Missing screws, scratches, etc.)</label>
            <textarea name="repair_notes" class="form-control" rows="2"></textarea>
        </div>

        <div class="form-actions" style="margin-top: 2rem;">
            <button type="button" onclick="history.back()" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Create Job Ticket</button>
        </div>
    </form>
</div>

<style>
    .section-title {
        color: var(--primary-glow);
        margin-bottom: 1rem;
        font-size: 1.1rem;
        border-bottom: 1px solid var(--border-glass);
        padding-bottom: 0.5rem;
    }
    
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--border-glass);
        border-radius: 0.5rem;
        color: #fff;
        font-family: inherit;
    }

    .form-control option {
        background-color: #1e293b; /* Dark slate background for options */
        color: #fff;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }
    
    .btn-secondary {
        background: transparent;
        border: 1px solid var(--border-glass);
        color: var(--text-muted);
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        cursor: pointer;
    }
    
    .text-danger { color: #ef4444; }
    
    @media (max-width: 640px) {
        .grid-2 { grid-template-columns: 1fr; }
    }
</style>
@endsection
