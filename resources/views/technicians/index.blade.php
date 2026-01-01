@extends('layouts.app')

@section('title', 'Manage Technicians')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Technicians</h2>
        <p class="text-muted">Manage your technical staff</p>
    </div>
    <a href="{{ route('technicians.create') }}" class="btn-primary">
        <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> New Technician
    </a>
</div>

<div class="card glass">
    <div class="toolbar-container">
        <!-- Search Form -->
        <form action="{{ route('technicians.index') }}" method="GET" class="search-form">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or specialty..." class="search-input">
            </div>
        </form>
    </div>

    <div class="table-responsive">
    <table class="data-table">
        <thead>
            <tr>
                <th style="text-align: left;">Name</th>
                <th>Role</th>
                <th>Specialty</th>
                <th>Contact</th>
                <th>Jobs Completed</th>
                <th>Performance</th>
                <th style="width: 120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($technicians as $tech)
            <tr class="clickable-row" onclick="window.location='{{ route('technicians.edit', $tech->id) }}'">
                <td style="text-align: left;">
                    <div class="tech-name">{{ $tech->user->name }}</div>
                    <div class="tech-id">ID: #{{ $tech->id }}</div>
                </td>
                <td>
                    <span class="badge {{ $tech->user->role == 'admin' ? 'badge-admin' : 'badge-tech' }}">
                        {{ ucfirst($tech->user->role) }}
                    </span>
                </td>
                <td>{{ $tech->specialty ?? 'General' }}</td>
                <td>
                    <div style="font-size: 0.9rem;">{{ $tech->user->phone ?? 'N/A' }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $tech->user->email }}</div>
                </td>
                <td>
                    <span style="font-weight: 600; color: var(--primary);">{{ $tech->completed_jobs_count }}</span>
                </td>
                <td>{{ number_format($tech->performance_score, 1) }} / 5.0</td>
                <td onclick="event.stopPropagation()">
                    <a href="{{ route('technicians.edit', $tech->id) }}" class="action-icon edit-icon" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('technicians.destroy', $tech->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this technician? This will also delete their user account.');">
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
                <td colspan="7" class="text-center text-muted" style="padding: 3rem;">
                    <i class="fas fa-user-slash" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                    No technicians found matching your search.
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
