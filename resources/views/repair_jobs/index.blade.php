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
        <a href="{{ route('repair-jobs.index', ['status' => 'all']) }}" class="filter-btn {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">All</a>
        <a href="{{ route('repair-jobs.index', ['status' => 'pending']) }}" class="filter-btn {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('repair-jobs.index', ['status' => 'in_progress']) }}" class="filter-btn {{ request('status') == 'in_progress' ? 'active' : '' }}">In Progress</a>
        <a href="{{ route('repair-jobs.index', ['status' => 'waiting_for_parts']) }}" class="filter-btn {{ request('status') == 'waiting_for_parts' ? 'active' : '' }}">Waiting</a>
        <a href="{{ route('repair-jobs.index', ['status' => 'completed']) }}" class="filter-btn {{ request('status') == 'completed' ? 'active' : '' }}">Completed</a>
        <a href="{{ route('repair-jobs.index', ['status' => 'delivered']) }}" class="filter-btn {{ request('status') == 'delivered' ? 'active' : '' }}">Delivered</a>
        <a href="{{ route('repair-jobs.index', ['status' => 'cancelled']) }}" class="filter-btn {{ request('status') == 'cancelled' ? 'active' : '' }}">Cancelled</a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 140px;">Job Number</th>
                <th style="width: 18%;">Customer</th>
                <th style="width: 22%;">Device</th>
                <th style="width: 15%;">Technician</th>
                <th style="width: 200px;">Status</th>
                <th style="width: 120px;">Date</th>
                <th style="width: 160px;" class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
            <tr class="clickable-row" onclick="window.location='{{ route('repair-jobs.edit', $job->id) }}'">
                <td style="font-family: monospace; font-weight: 600; color: var(--primary);">{{ $job->job_number }}</td>
                <td>
                    <div class="text-truncate" style="max-width: 150px;" title="{{ $job->customer->name }}">
                        {{ $job->customer->name }}
                    </div>
                </td>
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
    .data-table { 
        width: 100%; 
        border-collapse: collapse; 
    }
    
    .filters {
        display: flex;
        gap: 0.5rem;
        padding: 1rem;
        border-bottom: 1px solid var(--border-glass);
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: thin;
    }
    
    .filter-btn {
        background: transparent;
        border: 1px solid var(--border-glass);
        color: var(--text-muted);
        padding: 0.4rem 1rem;
        border-radius: 2rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }
    
    .filter-btn:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-main);
        border-color: rgba(255,255,255,0.2);
    }
    
    .filter-btn.active {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border-color: rgba(59, 130, 246, 0.4);
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.1);
    }

    .clickable-row {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .clickable-row:hover {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }
    
    .data-table th, .data-table td { 
        padding: 1rem; 
        text-align: left; 
        border-bottom: 1px solid var(--border-glass); 
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
