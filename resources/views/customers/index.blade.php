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
        <form action="{{ route('customers.index') }}" method="GET" class="search-form">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or phone..." class="search-input">
            </div>
        </form>
    </div>

    <div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align: left;">Customer Details</th>
                <th>Contact Info</th>
                <th>Address</th>
                <th>History</th>
                <th style="text-align: right;">Total Due</th>
                <th style="width: 140px; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr class="clickable-row" onclick="window.location='{{ route('customers.ledger', $customer->id) }}'">
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
                    <div style="display: flex; gap: 0.5rem;">
                        <span class="badge badge-jobs" title="Repair Jobs">
                            <i class="fas fa-tools" style="font-size: 0.7rem; margin-right: 4px;"></i> {{ $customer->repairs_count }}
                        </span>
                        <span class="badge badge-sales" style="background: rgba(34, 197, 94, 0.1); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2);" title="Direct Sales">
                            <i class="fas fa-shopping-cart" style="font-size: 0.7rem; margin-right: 4px;"></i> {{ $customer->sales_count }}
                        </span>
                    </div>
                </td>
                <td style="text-align: right;">
                    @if($customer->total_due > 0)
                        <span style="font-weight: bold; color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 2px 8px; border-radius: 4px;">
                            LKR {{ number_format($customer->total_due, 2) }}
                        </span>
                    @else
                        <span style="color: var(--text-muted); font-size: 0.9rem;">-</span>
                    @endif
                </td>
                <td onclick="event.stopPropagation()" style="white-space: nowrap; text-align: center;">
                    <a href="{{ route('customers.ledger', $customer->id) }}" class="action-icon" title="View Ledger" style="color: var(--primary); margin-right: 8px;">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </a>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="action-icon edit-icon" title="Edit" style="margin-right: 8px;">
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
