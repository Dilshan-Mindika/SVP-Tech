@extends('layouts.app')

@section('title', 'Manage Customers')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Customers</h2>
        <p class="text-muted">Manage client profiles and history</p>
    </div>
    <a href="{{ route('customers.create') }}" class="btn-primary">
        <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> New Customer
    </a>
</div>

<div class="card glass">
    <div class="toolbar-container">
        <!-- Search Form -->
        <form action="{{ route('customers.index') }}" method="GET" class="search-form" style="width: 100%;">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or phone..." class="search-input">
            </div>
        </form>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align: left;">Customer Details</th>
                <th>Contact Info</th>
                <th>Address</th>
                <th>Jobs</th>
                <th style="width: 120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr class="clickable-row" onclick="window.location='{{ route('customers.edit', $customer->id) }}'">
                <td style="text-align: left;">
                    <div class="table-text-main">{{ $customer->name }}</div>
                    <div class="table-text-sub">ID: #{{ $customer->id }}</div>
                </td>
                <td>
                    <div style="font-size: 0.95rem; font-weight: 500;" class="table-text-main">{{ $customer->phone ?? 'N/A' }}</div>
                    <div class="table-text-sub">{{ $customer->email ?? '-' }}</div>
                </td>
                <td style="max-width: 250px; font-size: 0.9rem;" class="text-truncate table-date-muted">
                    {{ Str::limit($customer->address, 40) ?? '-' }}
                </td>
                <td>
                    <span class="badge badge-jobs">
                        {{ $customer->repair_jobs_count }} Jobs
                    </span>
                </td>
                <td onclick="event.stopPropagation()">
                    <a href="{{ route('customers.edit', $customer->id) }}" class="action-icon edit-icon" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-icon delete-icon" title="Delete" style="border:none; background:transparent; padding:0; cursor:pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted" style="padding: 3rem;">
                    <i class="fas fa-users-slash" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                    No customers found matching your search.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .clickable-row:hover {
        background-color: rgba(255, 255, 255, 0.02);
    }
    [data-theme="light"] .clickable-row:hover {
        background-color: #f8fafc;
    }
</style>
@endsection
