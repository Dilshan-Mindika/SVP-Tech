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
                <th>Job ID</th>
                <th>Customer</th>
                <th>Device</th>
                <th>Technician</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
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
                    <span class="status-badge status-{{ $job->repair_status }}">
                        {{ ucfirst(str_replace('_', ' ', $job->repair_status)) }}
                    </span>
                </td>
                <td>{{ $job->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('repair-jobs.show', $job->id) }}" class="btn-sm">View</a>
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

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-glass); }
    .data-table th { color: var(--text-muted); font-weight: 500; font-size: 0.9rem; }
    
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: capitalize;
    }
    
    .status-pending { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
    .status-in_progress { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
    .status-completed { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    
    .btn-sm { color: var(--primary-glow); text-decoration: none; font-size: 0.9rem; }
    .text-center { text-align: center; }
</style>
@endsection
