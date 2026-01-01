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
    <div class="toolbar-container">
        <!-- Search Form -->
        <form action="{{ route('repair-jobs.index') }}" method="GET" class="search-form">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search jobs, customers, devices..." class="search-input">
                @if(request('status') && request('status') != 'all')
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
            </div>
        </form>

        <!-- Status Filters -->
        <div class="filters">
            <a href="{{ route('repair-jobs.index', ['status' => 'all', 'search' => request('search')]) }}" class="filter-btn {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">All</a>
            <a href="{{ route('repair-jobs.index', ['status' => 'pending', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('repair-jobs.index', ['status' => 'in_progress', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'in_progress' ? 'active' : '' }}">In Progress</a>
            <a href="{{ route('repair-jobs.index', ['status' => 'waiting_for_parts', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'waiting_for_parts' ? 'active' : '' }}">Waiting</a>
            <a href="{{ route('repair-jobs.index', ['status' => 'completed', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'completed' ? 'active' : '' }}">Completed</a>
            <a href="{{ route('repair-jobs.index', ['status' => 'delivered', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'delivered' ? 'active' : '' }}">Delivered</a>
            <a href="{{ route('repair-jobs.index', ['status' => 'cancelled', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'cancelled' ? 'active' : '' }}">Cancelled</a>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 140px;">Job Number</th>
                <th style="width: 15%;">Customer</th>
                <th style="width: 20%;">Device</th>
                <th style="width: 15%;">Technician</th>
                <th style="width: 180px;">Status</th>
                <th style="width: 110px;">Date</th>
                <th style="width: 100px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
            <tr class="clickable-row" onclick="window.location='{{ route('repair-jobs.edit', $job->id) }}'">
                <td style="font-family: monospace; font-weight: 600; color: var(--primary);">{{ $job->job_number }}</td>
                <td>
                    <div class="text-truncate" style="max-width: 180px; margin: 0 auto;" title="{{ $job->customer->name }}">
                        {{ $job->customer->name }}
                    </div>
                </td>
                <td>{{ $job->laptop_brand }} {{ $job->laptop_model }}</td>
                <td>{{ $job->technician ? $job->technician->user->name : 'Unassigned' }}</td>
                <td>
                    <form action="{{ route('repair-jobs.update-status', $job->id) }}" method="POST" onclick="event.stopPropagation()">
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
                <td>{{ $job->created_at->format('M d') }}</td>
                <td onclick="event.stopPropagation()">
                    <a href="{{ route('repair-jobs.edit', $job->id) }}" class="action-icon edit-icon" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="{{ route('invoice-preview', $job->id) }}" class="action-icon invoice-icon" title="View Invoice">
                        <i class="fas fa-file-invoice"></i>
                    </a>
                    <form action="{{ route('repair-jobs.destroy', $job->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this job?');">
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
    
    .toolbar-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid var(--border-glass);
    }

    @media (min-width: 768px) {
        .toolbar-container {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    .search-box {
        position: relative;
        flex: 1;
        max-width: 500px; /* Increased width */
    }

    .search-input {
        width: 100%;
        padding: 0.8rem 1rem 0.8rem 3.5rem !important; /* Force padding */
        background: rgba(0, 0, 0, 0.4); 
        border: 1px solid var(--border-glass);
        border-radius: 2rem;
        color: #fff;
        font-family: inherit;
        font-size: 1rem;
        transition: all 0.2s;
        line-height: normal; /* Fix vertical alignment */
    }
    
    .search-input::placeholder {
        color: #9ca3af;
        opacity: 1;
    }

    .search-input:focus {
        background: rgba(0, 0, 0, 0.6);
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .search-icon {
        position: absolute;
        left: 1.5rem; /* Adjusted for larger padding */
        top: 50%;
        transform: translateY(-50%);
        color: #d1d5db;
        pointer-events: none;
        font-size: 1.1rem;
        width: auto;
        height: auto;
        display: block;
    }
    
    .filters {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: thin;
        /* create padding without breaking layout */
        padding-bottom: 0.2rem;
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
        text-align: center; 
        border-bottom: 1px solid var(--border-glass); 
    }

    .data-table th { color: var(--text-muted); font-weight: 500; font-size: 0.9rem; }
    
    .action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        color: var(--text-muted);
        transition: all 0.2s;
        margin-left: 0.25rem;
        vertical-align: middle; /* Ensure alignment between a and button tags */
        text-decoration: none; /* For a tags */
    }
    
    .action-icon:hover {
        background: rgba(255, 255, 255, 0.1);
        color: var(--text-main);
    }
    
    .edit-icon:hover { color: var(--primary); background: rgba(59, 130, 246, 0.15); }
    .invoice-icon:hover { color: var(--success); background: rgba(34, 197, 94, 0.15); }
    .delete-icon:hover { color: var(--danger); background: rgba(239, 68, 68, 0.15); }

    /* Specific overrides */
    .data-table td:nth-child(5) { /* Status Column - allow overflow for dropdown */
        overflow: visible; 
    }
    .data-table td:last-child { /* Actions Column */
        text-align: center; 
        overflow: visible;
        white-space: nowrap; /* Keep icons on same line */
    }
</style>
@endsection
