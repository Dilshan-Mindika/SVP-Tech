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
            <label>Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" required placeholder="Jane Doe" class="form-control">
        </div>

        <div class="form-group">
            <label>Customer Type</label>
            <select name="type" class="form-control">
                <option value="normal">Individual (Normal)</option>
                <option value="shop">Shop / Business</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="client@example.com" class="form-control">
        </div>
        
        <div class="form-group">
            <label>Phone Number <span class="text-danger">*</span></label>
            <input type="text" name="phone" placeholder="+1 234 567 890" class="form-control">
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
@endsection
