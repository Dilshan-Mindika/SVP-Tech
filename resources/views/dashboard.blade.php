@extends('layouts.app')

@section('title', 'Dashboard - SVP Tech')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Dashboard</h2>
        <p class="text-muted">Welcome back, {{ auth()->user()->name }}</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <h3>Active Jobs</h3>
        <p class="stat-number">12</p>
        <p class="stat-desc">3 jobs overdue</p>
    </div>
    
    <div class="stat-card">
        <h3>Pending Invoices</h3>
        <p class="stat-number">5</p>
        <p class="stat-desc">$1,250.00 estimated value</p>
    </div>
    
    <div class="stat-card">
        <h3>Technicians</h3>
        <p class="stat-number">4</p>
        <p class="stat-desc">All active and assigned</p>
    </div>
    
    <div class="stat-card">
        <h3>Est. Monthly Revenue</h3>
        <p class="stat-number">$15,400</p>
        <p class="stat-desc">+12% from last month</p>
    </div>
</div>

<!-- Quick Actions / Recent Activity Placeholder -->
<div class="card mt-4">
    <div class="card-header">
        <h3>Recent Repair Jobs</h3>
    </div>
    <div class="card-body">
        <p class="text-muted text-center" style="padding: 2rem;">No recent activity to display.</p>
    </div>
</div>
@endsection
