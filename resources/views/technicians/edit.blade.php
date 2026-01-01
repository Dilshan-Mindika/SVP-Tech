@extends('layouts.app')

@section('title', 'Edit Technician')

@section('content')
<div class="page-header">
    <h2>Edit Technician</h2>
</div>

<div class="card glass" style="max-width: 600px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('technicians.update', $technician->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- User Details -->
        <h3 class="section-title">Account Details</h3>
        
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" required value="{{ old('name', $technician->user->name) }}" class="form-control">
        </div>
        
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required value="{{ old('email', $technician->user->email) }}" class="form-control">
        </div>
        
        <div class="form-group">
            <label>Password (Leave blank to keep current)</label>
            <input type="password" name="password" placeholder="New Password" class="form-control">
        </div>

        <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control">
                <option value="technician" {{ $technician->user->role == 'technician' ? 'selected' : '' }}>Technician</option>
                <option value="admin" {{ $technician->user->role == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $technician->user->phone) }}" class="form-control" placeholder="Phone Number">
        </div>

        <!-- Tech Details -->
        <h3 class="section-title" style="margin-top: 2rem;">Technician Profile</h3>
        
        <div class="form-group">
            <label>Specialty</label>
            <input type="text" name="specialty" value="{{ old('specialty', $technician->specialty) }}" class="form-control" placeholder="e.g. Motherboard Repair, Apple Devices">
        </div>

        <div class="form-actions" style="margin-top: 2rem;">
            <button type="button" onclick="history.back()" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Update Technician</button>
        </div>
    </form>
</div>

<style>
    .section-title {
        color: var(--primary);
        margin-bottom: 1rem;
        font-size: 1.1rem;
        border-bottom: 1px solid var(--border-glass);
        padding-bottom: 0.5rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--border-glass);
        border-radius: 0.5rem;
        color: #fff;
        font-family: inherit;
        display: block;
        margin-top: 0.5rem;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .form-control option {
        background-color: #1e293b;
        color: #f8fafc;
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
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-main);
    }
</style>
@endsection
