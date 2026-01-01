@extends('layouts.app')

@section('title', 'Add Part')

@section('content')
<div class="page-header">
    <h2>Add New Part</h2>
</div>

<div class="card glass" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf
        
        <div class="grid-2">
            <div class="form-group">
                <label>Part Name</label>
                <input type="text" name="name" required placeholder="e.g. 512GB NVMe SSD" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock_quantity" required placeholder="0" class="form-control">
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Brand</label>
                <input type="text" name="brand" placeholder="e.g. Samsung" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Model</label>
                <input type="text" name="model" placeholder="Model No." class="form-control">
            </div>
        </div>

        <div class="grid-3" style="margin-top: 1rem;">
            <div class="form-group">
                <label>Cost Price (LKR)</label>
                <input type="number" step="0.01" name="cost_price" required placeholder="0.00" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Selling Price (LKR)</label>
                <input type="number" step="0.01" name="selling_price" required placeholder="0.00" class="form-control">
            </div>

            <div class="form-group">
                <label>Low Stock Alert</label>
                <input type="number" name="low_stock_threshold" value="5" class="form-control">
            </div>
        </div>

        <div class="form-group" style="margin-top: 1rem;">
            <label>Description / Notes</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Additional details..."></textarea>
        </div>

        <div class="form-actions" style="margin-top: 2rem;">
            <a href="{{ route('inventory.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Add Part</button>
        </div>
    </form>
</div>

@endsection
