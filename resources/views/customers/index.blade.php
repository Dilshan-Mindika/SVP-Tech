@extends('layouts.app')

@section('title', 'Manage Customers')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Customers</h2>
        <p class="text-muted">Manage client profiles</p>
    </div>
    <a href="{{ route('customers.create') }}" class="btn-primary">Add New Customer</a>
</div>

<div class="card glass">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email ?? '-' }}</td>
                <td>{{ $customer->phone ?? '-' }}</td>
                <td>{{ Str::limit($customer->address, 30) }}</td>
                <td>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn-sm">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">No customers found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-glass); }
    .data-table th { color: var(--text-muted); font-weight: 500; font-size: 0.9rem; }
    .btn-sm { color: var(--primary-glow); text-decoration: none; font-size: 0.9rem; }
    .text-center { text-align: center; }
</style>
@endsection
