@extends('layouts.app')

@section('title', 'New Repair Job')

@section('content')
<div class="page-header">
    <h2>Create New Repair Job</h2>
</div>

<div class="card glass" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('repair-jobs.store') }}" method="POST">
        @csrf
        
        <h3 class="section-title">Customer Information</h3>
        <!-- Ideally this would be a search/select for existing customers -->
        <div class="form-group">
            <label>Customer Name</label>
            <input type="text" name="customer_search" placeholder="Search for customer...">
            <!-- Placeholder for Customer Select Logic -->
        </div>

        <h3 class="section-title" style="margin-top: 2rem;">Device Details</h3>
        <div class="grid-2">
            <div class="form-group">
                <label>Brand</label>
                <input type="text" name="laptop_brand" required placeholder="Dell, HP, Apple...">
            </div>
            <div class="form-group">
                <label>Model</label>
                <input type="text" name="laptop_model" required placeholder="XPS 15, MacBook Pro...">
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
    
    @media (max-width: 640px) {
        .grid-2 { grid-template-columns: 1fr; }
    }
</style>
@endsection
