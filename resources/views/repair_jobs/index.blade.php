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
                     <div class="flex flex-col gap-3">
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
                    <div class="flex flex-col text-xs text-muted gap-1">
                        <div title="Created"><i class="fas fa-plus-circle w-4 text-center text-blue-400"></i> {{ $job->created_at->format('M d') }}</div>
                        @if($job->completed_at)
                            <div title="Completed"><i class="fas fa-check-circle w-4 text-center text-green-400"></i> {{ $job->completed_at->format('M d') }}</div>
                        @endif
                        @if($job->delivered_at)
                            <div title="Delivered"><i class="fas fa-truck w-4 text-center text-purple-400"></i> {{ $job->delivered_at->format('M d') }}</div>
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
</style>
@endsection
