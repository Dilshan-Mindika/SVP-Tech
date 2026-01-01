@extends('layouts.app')

@section('title', 'Manage Technicians')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Technicians</h2>
        <p class="text-muted">Manage your technical staff</p>
    </div>
    <a href="{{ route('technicians.create') }}" class="btn-primary">Add New Technician</a>
</div>

<div class="card glass">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Specialty</th>
                <th>Jobs Completed</th>
                <th>Performance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($technicians as $tech)
            <tr>
                <td>{{ $tech->user->name }}</td>
                <td><span class="badge badge-tech">{{ $tech->user->role }}</span></td>
                <td>{{ $tech->specialty ?? 'General' }}</td>
                <td>{{ $tech->completed_jobs_count }}</td>
                <td>{{ number_format($tech->performance_score, 1) }} / 5.0</td>
                <td>
                    <a href="{{ route('technicians.edit', $tech->id) }}" class="btn-sm">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">No technicians found.</td>
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
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th, .data-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-glass);
    }
    
    .data-table th {
        color: var(--text-muted);
        font-weight: 500;
        font-size: 0.9rem;
    }
    
    .badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        background: rgba(139, 92, 246, 0.2);
        color: #ddd;
    }
    
    .btn-sm {
        color: var(--primary-glow);
        text-decoration: none;
        font-size: 0.9rem;
    }
    
    .text-center { text-align: center; }
</style>
@endsection
