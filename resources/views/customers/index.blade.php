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
                    <div style="font-weight: 600; color: #fff; font-size: 1rem;">{{ $customer->name }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">ID: #{{ $customer->id }}</div>
                </td>
                <td>
                    <div style="font-size: 0.95rem; font-weight: 500; color: #e2e8f0;">{{ $customer->phone ?? 'N/A' }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $customer->email ?? '-' }}</div>
                </td>
                <td style="max-width: 250px; color: #cbd5e1; font-size: 0.9rem;">
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
    /* Toolbar & Search (Shared Style) */
    .toolbar-container {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-glass);
    }

    .search-box {
        position: relative;
        max-width: 500px;
    }

    .search-input {
        width: 100%;
        padding: 0.8rem 1rem 0.8rem 3.5rem !important;
        background: rgba(0, 0, 0, 0.4); 
        border: 1px solid var(--border-glass);
        border-radius: 2rem;
        color: #fff;
        font-family: inherit;
        font-size: 1rem;
        transition: all 0.2s;
    }
    
    .search-input:focus {
        background: rgba(0, 0, 0, 0.6);
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .search-icon {
        position: absolute;
        left: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }

    /* Table Styles */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        padding: 1rem 1.5rem;
        color: #9ca3af;
        font-weight: 500;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        text-align: center;
        border-bottom: 1px solid var(--border-glass);
    }

    .data-table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        text-align: center;
        border-bottom: 1px solid var(--border-glass);
    }
    
    .data-table td:last-child {
        white-space: nowrap;
    }

    .clickable-row {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .clickable-row:hover {
        background-color: rgba(255, 255, 255, 0.02);
    }

    /* Badges */
    .badge {
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    
    .badge-jobs {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    /* Actions */
    .action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        color: #9ca3af;
        transition: all 0.2s;
        margin: 0 0.2rem;
    }

    .edit-icon:hover { color: var(--primary); background: rgba(59, 130, 246, 0.15); }
    .delete-icon:hover { color: var(--danger); background: rgba(239, 68, 68, 0.15); }
</style>
@endsection
