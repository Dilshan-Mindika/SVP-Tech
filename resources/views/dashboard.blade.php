@extends('layouts.app')

@section('title', 'Dashboard - SVP Tech')

@section('content')
<div class="dashboard-header">
    <h2>Dashboard</h2>
    <p class="text-muted">Welcome back, {{ auth()->user()->name }}</p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card glass">
        <h3>Active Jobs</h3>
        <p class="stat-number">12</p>
        <p class="stat-desc">3 overdue</p>
    </div>
    <div class="stat-card glass">
        <h3>Pending Invoices</h3>
        <p class="stat-number">5</p>
        <p class="stat-desc">$1,250.00 value</p>
    </div>
    <div class="stat-card glass">
        <h3>Technicians</h3>
        <p class="stat-number">4</p>
        <p class="stat-desc">All active</p>
    </div>
    <div class="stat-card glass">
        <h3>Monthly Revenue</h3>
        <p class="stat-number">$15,400</p>
        <p class="stat-desc">+12% from last month</p>
    </div>
</div>

<style>
    .dashboard-header {
        margin-bottom: 2rem;
    }
    .text-muted {
        color: var(--text-muted);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
    }
    
    .stat-card {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-glow);
    }
    
    .stat-desc {
        color: var(--text-muted);
        font-size: 0.9rem;
    }
</style>
@endsection
