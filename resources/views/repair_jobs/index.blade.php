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
        <!-- Advanced Filter Form -->
        <!-- Advanced Filter Toolbar -->
        <form action="{{ route('repair-jobs.index') }}" method="GET" class="filter-toolbar">
            
            <!-- Search -->
            <div class="search-group">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Job No, Customer, Device..." class="search-input">
            </div>

            <!-- Filters Group -->
            <div class="filter-group">
                <!-- Technician Filter -->
                <div class="select-wrapper">
                    <i class="fas fa-user-friends select-icon"></i>
                    <select name="technician_id" class="filter-select">
                        <option value="">All Technicians</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>
                                {{ explode(' ', $tech->user->name)[0] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div class="date-group">
                    <div class="date-input-wrapper">
                        <span class="date-label">From</span>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-date">
                    </div>
                    <span class="date-separator">to</span>
                    <div class="date-input-wrapper">
                        <span class="date-label">To</span>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-date">
                    </div>
                </div>

                <!-- Actions -->
                <button type="submit" class="btn-filter" title="Apply Filters">
                    <i class="fas fa-filter"></i> <span>Filter</span>
                </button>
                
                @if(request('search') || request('technician_id') || request('date_from') || request('date_to'))
                    <a href="{{ route('repair-jobs.index', ['status' => request('status') ?? 'all']) }}" class="btn-clear" title="Clear Filters">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            @if(request('status') && request('status') != 'all')
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </form>

        <!-- Status Filters -->
        <!-- Status Filters -->
        <div class="filters">
            <a href="{{ route('repair-jobs.index', ['status' => 'all', 'search' => request('search')]) }}" class="filter-btn {{ !request('status') || request('status') == 'all' ? 'active' : '' }}">
                All <span class="count-badge">{{ $totalJobsCount }}</span>
            </a>
            <a href="{{ route('repair-jobs.index', ['status' => 'pending', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'pending' ? 'active' : '' }}">
                Pending <span class="count-badge">{{ $statusCounts['pending'] ?? 0 }}</span>
            </a>
            <a href="{{ route('repair-jobs.index', ['status' => 'in_progress', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'in_progress' ? 'active' : '' }}">
                In Progress <span class="count-badge">{{ $statusCounts['in_progress'] ?? 0 }}</span>
            </a>
            <a href="{{ route('repair-jobs.index', ['status' => 'waiting_for_parts', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'waiting_for_parts' ? 'active' : '' }}">
                Waiting <span class="count-badge">{{ $statusCounts['waiting_for_parts'] ?? 0 }}</span>
            </a>
            <a href="{{ route('repair-jobs.index', ['status' => 'completed', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'completed' ? 'active' : '' }}">
                Completed <span class="count-badge">{{ $statusCounts['completed'] ?? 0 }}</span>
            </a>
            <a href="{{ route('repair-jobs.index', ['status' => 'delivered', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'delivered' ? 'active' : '' }}">
                Delivered <span class="count-badge">{{ $statusCounts['delivered'] ?? 0 }}</span>
            </a>
            <a href="{{ route('repair-jobs.index', ['status' => 'cancelled', 'search' => request('search')]) }}" class="filter-btn {{ request('status') == 'cancelled' ? 'active' : '' }}">
                Cancelled <span class="count-badge">{{ $statusCounts['cancelled'] ?? 0 }}</span>
            </a>
        </div>
    </div>

    <div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 100px;">Job No</th>
                <th style="width: 15%;">Customer</th>
                <th style="width: 20%;">Device</th>
                <th style="width: 15%;">Technician</th>
                <th style="width: 160px;">Status</th>
                <th style="width: 140px;">Timeline</th>
                <th style="width: 90px; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
            <tr class="clickable-row" onclick="window.location='{{ route('repair-jobs.edit', $job->id) }}'">
                <td style="font-family: monospace; font-weight: 600; color: var(--primary);">{{ $job->job_number }}</td>
                <td>
                    <div class="text-truncate" style="max-width: 180px;" title="{{ $job->customer->name }}">
                        {{ $job->customer->name }}
                    </div>
                </td>
                <td>{{ $job->laptop_brand }} {{ $job->laptop_model }}</td>
                <td>
                    <form action="{{ route('repair-jobs.assign-technician', $job->id) }}" method="POST" onclick="event.stopPropagation()">
                        @csrf
                        @method('PATCH')
                        <select name="technician_id" onchange="this.form.submit()" class="tech-select text-xs">
                            <option value="">-- Unassigned --</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $job->technician_id == $tech->id ? 'selected' : '' }}>
                                    {{ explode(' ', $tech->user->name)[0] }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </td>
                <td>
                     <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <form action="{{ route('repair-jobs.update-status', $job->id) }}" method="POST" onclick="event.stopPropagation()">
                            @csrf
                            @method('PATCH')
                            <select name="repair_status" onchange="this.form.submit()" class="status-pill status-{{ $job->repair_status }}">
                                <option value="pending" {{ $job->repair_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $job->repair_status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="waiting_for_parts" {{ $job->repair_status == 'waiting_for_parts' ? 'selected' : '' }}>Waiting</option>
                                <option value="completed" {{ $job->repair_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="delivered" {{ $job->repair_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $job->repair_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </form>
                        <form action="{{ route('repair-jobs.update-payment-status', $job->id) }}" method="POST" onclick="event.stopPropagation()">
                            @csrf
                            @method('PATCH')
                            <select name="payment_status" onchange="this.form.submit()" class="status-pill 
                                {{ $job->payment_status === 'paid' ? 'bg-green-500/10 text-green-500 border-green-500/20' : '' }}
                                {{ $job->payment_status === 'partial' ? 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20' : '' }}
                                {{ ($job->payment_status === 'pending' || $job->payment_status === 'unpaid') ? 'bg-red-500/10 text-red-500 border-red-500/20' : '' }}
                            ">
                                <option value="pending" {{ $job->payment_status == 'pending' ? 'selected' : '' }}>Unpaid</option>
                                <option value="partial" {{ $job->payment_status == 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ $job->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </form>
                    </div>
                </td>
                <td>
                     <div class="flex flex-col text-xs gap-1" style="font-weight: 500;">
                        <div class="timeline-created" title="Created">
                            <i class="fas fa-plus-circle w-4 text-center"></i> {{ $job->created_at->format('M d') }}
                        </div>
                        @if($job->completed_at)
                            <div class="timeline-completed" title="Completed">
                                <i class="fas fa-check-circle w-4 text-center"></i> {{ $job->completed_at->format('M d') }}
                            </div>
                        @endif
                        @if($job->delivered_at)
                            <div class="timeline-delivered" title="Delivered">
                                <i class="fas fa-truck w-4 text-center"></i> {{ $job->delivered_at->format('M d') }}
                            </div>
                        @endif
                    </div>
                </td>
                <td onclick="event.stopPropagation()" class="text-center">
                    <div class="flex justify-center gap-1">
                        <a href="{{ route('repair-jobs.edit', $job->id) }}" class="action-icon edit-icon" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('invoice-preview', $job->id) }}" class="action-icon view-icon" title="View Invoice">
                            <i class="fas fa-file-invoice"></i>
                        </a>
                        <form action="{{ route('repair-jobs.destroy', $job->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this job?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-icon delete-icon" title="Delete" style="border:none; background:transparent; padding:0; cursor:pointer; width: 32px; height: 32px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-8">No repair jobs found.</td>
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
    .data-table td:nth-child(5) { /* Status Column - allow overflow for dropdown */
        overflow: visible; 
    }
    .data-table td:last-child { /* Actions Column */
        text-align: center; 
        overflow: visible;
        white-space: nowrap; /* Keep icons on same line */
    }

    /* Enhanced Filter Toolbar Styles */
    .filter-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        background: rgba(0, 0, 0, 0.2);
        padding: 0.75rem;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        margin-bottom: 1rem;
    }

    .search-group {
        position: relative;
        flex: 1;
        min-width: 250px;
    }

    .search-group .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }

    .search-group .search-input {
        width: 100%;
        padding-left: 2.5rem !important;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        height: 42px;
    }

    .filter-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .select-wrapper {
        position: relative;
        min-width: 180px;
    }

    .select-wrapper .select-icon {
        position: absolute;
        left: 0.8rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
        font-size: 0.85rem;
    }

    .filter-select {
        width: 100%;
        padding: 0 1rem 0 2.2rem;
        height: 42px;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: var(--text-main);
        appearance: none;
        cursor: pointer;
    }

    .date-group {
        display: flex;
        align-items: center;
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 0 0.5rem;
        height: 42px;
    }

    .date-input-wrapper {
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .date-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
    }

    .filter-date {
        background: transparent;
        border: none;
        color: var(--text-main);
        font-family: inherit;
        font-size: 0.9rem;
        outline: none;
        width: 110px;
        padding: 0.2rem 0;
        color-scheme: dark; /* Force dark mode native UI for calendar */
        cursor: pointer;
    }

    .filter-date::-webkit-calendar-picker-indicator {
        opacity: 0.6;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .filter-date:hover::-webkit-calendar-picker-indicator {
        opacity: 1;
    }

    [data-theme="light"] .filter-date {
        color-scheme: light;
    }

    .date-separator {
        color: var(--text-muted);
        font-size: 0.8rem;
        margin: 0 0.5rem;
    }

    .btn-filter, .btn-clear {
        height: 42px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 1rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-filter {
        background: var(--primary);
        color: white;
        gap: 0.5rem;
    }

    .btn-filter:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-clear {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-muted);
        width: 42px;
        padding: 0;
    }

    .btn-clear:hover {
        background: rgba(239, 68, 68, 0.15);
        color: var(--danger);
    }

    [data-theme="light"] .filter-toolbar,
    [data-theme="light"] .search-group .search-input,
    [data-theme="light"] .filter-select,
    [data-theme="light"] .date-group {
        background: #ffffff;
        border-color: #e2e8f0;
    }

    [data-theme="light"] .btn-clear {
        background: #f1f5f9;
        color: #64748b;
    }

    [data-theme="light"] .filter-toolbar {
        background: #f8fafc;
    }

@endsection
