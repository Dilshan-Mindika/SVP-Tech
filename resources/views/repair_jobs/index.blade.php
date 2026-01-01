@extends('layouts.app')

@section('title', 'Repair Jobs')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Repair Jobs</h2>
        <p class="text-muted">Track and manage repair requests</p>
    </div>
    <a href="{{ route('repair-jobs.create') }}" class="btn-primary">New Repair Job</a>
</div>

<div class="card glass">
    <div class="filters">
        <button class="filter-btn active">All</button>
        <button class="filter-btn">Pending</button>
        <button class="filter-btn">In Progress</button>
        <button class="filter-btn">Completed</button>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 80px;">Job ID</th>
                <th style="width: 20%;">Customer</th>
                <th style="width: 25%;">Device</th>
                <th style="width: 15%;">Technician</th>
                <th style="width: 150px;">Status</th>
                <th style="width: 120px;">Date</th>
                <th style="width: 160px;" class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
            <tr>
                <td>#{{ $job->id }}</td>
                <td>{{ $job->customer->name }}</td>
                <td>{{ $job->laptop_brand }} {{ $job->laptop_model }}</td>
                <td>{{ $job->technician ? $job->technician->user->name : 'Unassigned' }}</td>
                <td>
                    <form action="{{ route('repair-jobs.update-status', $job->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <select name="repair_status" onchange="this.form.submit()" class="status-select status-{{ $job->repair_status }}">
                            <option value="pending" {{ $job->repair_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $job->repair_status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="waiting_for_parts" {{ $job->repair_status == 'waiting_for_parts' ? 'selected' : '' }}>Waiting</option>
                            <option value="completed" {{ $job->repair_status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $job->repair_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </form>
                </td>
                <td>{{ $job->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('repair-jobs.edit', $job->id) }}" class="btn-sm" style="margin-right:0.5rem;">Edit</a>
                    <a href="{{ route('invoice-preview', $job->id) }}" class="btn-sm">Invoice</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">No repair jobs found.</td>
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
    
    .filters {
        display: flex;
        gap: 0.5rem;
        padding: 1rem;
        border-bottom: 1px solid var(--border-glass);
    }
    
    .filter-btn {
        background: transparent;
        border: 1px solid var(--border-glass);
        color: var(--text-muted);
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .filter-btn.active, .filter-btn:hover {
        background: rgba(139, 92, 246, 0.2);
        color: #fff;
        border-color: var(--primary);
    }

    /* Utility Helper Classes */
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 1px; /* Triggers truncation in grid/flex contexts, usually works in table-layout:fixed too */
    }

    /* Enforce fixed layout for width controls to work well */
    .data-table { 
        width: 100%; 
        border-collapse: collapse; 
        table-layout: fixed; 
    }
    
    .data-table th, .data-table td { 
        padding: 1rem; 
        text-align: left; 
        border-bottom: 1px solid var(--border-glass); 
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .data-table th { color: var(--text-muted); font-weight: 500; font-size: 0.9rem; }
    
    /* Specific overrides */
    .data-table td:nth-child(5) { /* Status Column - allow overflow for dropdown */
        overflow: visible; 
    }
    .data-table td:last-child { /* Actions Column - text align right */
        text-align: right; 
        overflow: visible;
    }
</style>
@endsection
