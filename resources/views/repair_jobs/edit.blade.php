@extends('layouts.app')

@section('title', 'Edit Repair Job #' . $repairJob->id)

@section('content')
<div class="page-header">
    <h2>Edit Repair Job #{{ $repairJob->id }}</h2>
</div>

<div class="card glass" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('repair-jobs.update', $repairJob->id) }}" method="POST"
          x-data="{ 
              status: '{{ $repairJob->repair_status }}',
              parts: {{ $repairJob->parts_used_cost ?? 0 }}, 
              labor: {{ $repairJob->labor_cost ?? 0 }}, 
              price: {{ $repairJob->final_price ?? 0 }},
              get profit() { return this.price - (Number(this.parts) + Number(this.labor)); }
          }">
        @csrf
        @method('PUT')
        
        <div class="grid-2">
            <!-- Status & Tech Assignment -->
            <div class="form-group">
                <label>Job Status</label>
                <select name="repair_status" class="form-control" x-model="status">
                    <option value="pending" {{ $repairJob->repair_status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ $repairJob->repair_status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="waiting_for_parts" {{ $repairJob->repair_status == 'waiting_for_parts' ? 'selected' : '' }}>Waiting for Parts</option>
                    <option value="completed" {{ $repairJob->repair_status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $repairJob->repair_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label>Assign Technician</label>
                <select name="technician_id" class="form-control">
                    <option value="">-- Unassigned --</option>
                    @foreach(\App\Models\Technician::with('user')->get() as $tech)
                        <option value="{{ $tech->id }}" {{ $repairJob->technician_id == $tech->id ? 'selected' : '' }}>
                            {{ $tech->user->name }} ({{ $tech->specialty }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <h3 class="section-title" style="margin-top: 2rem;">Job Details</h3>
        
        <div class="grid-2">
            <div class="form-group">
                <label>Laptop Brand</label>
                <input type="text" name="laptop_brand" value="{{ $repairJob->laptop_brand }}" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label>Laptop Model</label>
                <input type="text" name="laptop_model" value="{{ $repairJob->laptop_model }}" class="form-control" readonly>
            </div>
        </div>

        <div class="form-group">
            <label>Fault Description</label>
            <textarea name="fault_description" class="form-control" rows="3">{{ $repairJob->fault_description }}</textarea>
        </div>

        <div class="form-group">
            <label>Work Done / Parts Added (Notes)</label>
            <textarea name="repair_notes" class="form-control" rows="3" placeholder="e.g. Replaced LCD Screen, Added 8GB RAM...">{{ $repairJob->repair_notes }}</textarea>
        </div>

        <h3 class="section-title" style="margin-top: 2rem;" x-show="status === 'completed'" x-transition x-cloak>Financials & Completion</h3>
        
        <div class="grid-3" x-show="status === 'completed'" x-transition x-cloak>
            <div class="form-group">
                <label>Parts Cost (Internal)</label>
                <div class="input-group">
                    <span class="prefix">$</span>
                    <input type="number" step="0.01" name="parts_used_cost" x-model="parts" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>Labor Cost (Internal)</label>
                <div class="input-group">
                    <span class="prefix">$</span>
                    <input type="number" step="0.01" name="labor_cost" x-model="labor" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>Final Customer Price</label>
                <div class="input-group">
                    <span class="prefix">$</span>
                    <input type="number" step="0.01" name="final_price" x-model="price" class="form-control" style="border-color: var(--success);">
                </div>
            </div>

            <div class="form-group" style="grid-column: 1 / -1; margin-top: 1rem; background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 6px;">
                <label style="margin-bottom: 0;">Estimated Net Profit</label>
                <div style="font-size: 1.5rem; font-weight: 700;" :style="{ color: profit >= 0 ? 'var(--success)' : 'var(--danger)' }">
                    $<span x-text="profit.toFixed(2)"></span>
                </div>
            </div>
        </div>

        <div class="form-actions" style="margin-top: 2rem;">
            <a href="{{ route('repair-jobs.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Job</button>
        </div>
    </form>
</div>

<style>
    [x-cloak] { display: none !important; }
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

    .grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1.5rem;
    }

    .input-group {
        position: relative;
    }

    .input-group .prefix {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-weight: 500;
    }

    .input-group input {
        padding-left: 2rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--border-glass);
        border-radius: 0.5rem;
        color: #fff;
    }

    .form-control[readonly] {
        background: rgba(0, 0, 0, 0.1);
        color: var(--text-muted);
        cursor: not-allowed;
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
        text-decoration: none;
        display: inline-block;
    }

    @media (max-width: 640px) {
        .grid-2, .grid-3 { grid-template-columns: 1fr; }
    }
</style>
@endsection
