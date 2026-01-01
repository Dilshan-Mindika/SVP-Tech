@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="page-header">
    <h2>Add New Customer</h2>
</div>

<div class="card glass" style="max-width: 600px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('customers.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" required placeholder="Jane Doe">
        </div>
        
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="client@example.com">
        </div>
        
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="+1 234 567 890">
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="3" placeholder="123 Main St, City"></textarea>
        </div>

        <div class="form-actions" style="margin-top: 2rem;">
            <button type="button" onclick="history.back()" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Create Customer</button>
        </div>
    </form>
</div>

<style>
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
</style>
@endsection
