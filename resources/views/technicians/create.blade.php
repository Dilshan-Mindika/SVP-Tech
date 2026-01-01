@extends('layouts.app')

@section('title', 'Add Technician')

@section('content')
<div class="page-header">
    <h2>Add New Technician</h2>
</div>

<div class="card glass" style="max-width: 600px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('technicians.store') }}" method="POST">
        @csrf
        
        <!-- User Details -->
        <h3 class="section-title">Account Details</h3>
        
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" required placeholder="John Doe">
        </div>
        
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="tech@svp.tech">
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Default Password">
        </div>

        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control">
                <option value="technician">Technician</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <!-- Tech Details -->
        <h3 class="section-title" style="margin-top: 2rem;">Technician Profile</h3>
        
        <div class="form-group">
            <label>Specialty</label>
            <input type="text" name="specialty" class="form-control" placeholder="e.g. Motherboard Repair, Apple Devices">
        </div>

        <div class="form-actions" style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <button type="button" onclick="history.back()" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Create Technician</button>
        </div>
    </form>
</div>

@endsection
