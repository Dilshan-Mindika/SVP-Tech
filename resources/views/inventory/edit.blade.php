@extends('layouts.app')

@section('title', 'Edit Part')

@section('content')
<div class="page-header">
    <h2>Edit Part</h2>
</div>

<div class="card glass" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('inventory.update', $part->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid-2">
            <div class="form-group">
                <label>Part Name</label>
                <input type="text" name="name" value="{{ $part->name }}" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock_quantity" value="{{ $part->stock_quantity }}" required class="form-control">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Brand</label>
                <input type="text" name="brand" value="{{ $part->brand }}" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Model</label>
                <input type="text" name="model" value="{{ $part->model }}" class="form-control">
            </div>
        </div>

        <div class="grid-3" style="margin-top: 1rem;">
            <div class="form-group">
                <label>Cost Price (LKR)</label>
                <input type="number" step="0.01" name="cost_price" value="{{ $part->cost_price }}" required class="form-control">
            </div>
            
            <div class="form-group">
                <label>Selling Price (LKR)</label>
                <input type="number" step="0.01" name="selling_price" value="{{ $part->selling_price }}" required class="form-control">
            </div>

            <div class="form-group">
                <label>Low Stock Alert</label>
                <input type="number" name="low_stock_threshold" value="{{ $part->low_stock_threshold }}" class="form-control">
            </div>
        </div>

        <div class="form-group" style="margin-top: 1rem;">
            <label>Description / Notes</label>
            <textarea name="description" class="form-control" rows="3">{{ $part->description }}</textarea>
        </div>

        <div class="form-actions" style="margin-top: 2rem;">
            <a href="{{ route('inventory.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Part</button>
        </div>
    </form>
</div>

<style>
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem; }
    
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--border-glass);
        border-radius: 0.5rem;
        color: #fff;
        font-family: inherit;
    }
    
    .form-actions { display: flex; gap: 1rem; justify-content: flex-end; }
    
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
