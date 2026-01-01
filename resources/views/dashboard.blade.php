@extends('layouts.app')

@section('title', 'Dashboard - SVP Tech')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Dashboard</h2>
        <p class="text-muted">Welcome back, {{ auth()->user()->name }}</p>
    </div>
</div>

<!-- Daily Stats Section -->
<h3 class="section-title">Daily Overview <span class="text-date">{{ now()->format('M d, Y') }}</span></h3>
<div class="stats-grid">
    <div class="stat-card">
        <h3>New Jobs</h3>
        <p class="stat-number">{{ $dailyJobsGot }}</p>
        <p class="stat-desc">Jobs received today</p>
    </div>
    
    <div class="stat-card">
        <h3>Completed</h3>
        <p class="stat-number">{{ $dailyJobsCompleted }}</p>
        <p class="stat-desc">Jobs finished today</p>
    </div>
    
    <div class="stat-card">
        <h3>Total Revenue</h3>
        <p class="stat-number text-primary">LKR {{ number_format($dailyRevenue, 2) }}</p>
        <p class="stat-desc">Total billed today</p>
    </div>

    <div class="stat-card">
        <h3>Total Cost</h3>
        <p class="stat-number">LKR {{ number_format($dailyCost, 2) }}</p>
        <p class="stat-desc">Parts + Labor today</p>
    </div>
    
    <div class="stat-card highlight-card">
        <h3>Net Profit</h3>
        <p class="stat-number {{ $dailyProfit >= 0 ? 'text-success' : 'text-danger' }}">
            LKR {{ number_format($dailyProfit, 2) }}
        </p>
        <p class="stat-desc">Daily earnings</p>
    </div>
</div>

<!-- Monthly Stats Section -->
<h3 class="section-title" style="margin-top: 2.5rem;">Monthly Overview <span class="text-date">{{ now()->format('F Y') }}</span></h3>
<div class="stats-grid">
    <div class="stat-card">
        <h3>New Jobs</h3>
        <p class="stat-number">{{ $monthlyJobsGot }}</p>
        <p class="stat-desc">Jobs received this month</p>
    </div>
    
    <div class="stat-card">
        <h3>Completed</h3>
        <p class="stat-number">{{ $monthlyJobsCompleted }}</p>
        <p class="stat-desc">Jobs finished this month</p>
    </div>
    
    <div class="stat-card">
        <h3>Total Revenue</h3>
        <p class="stat-number text-primary">LKR {{ number_format($monthlyRevenue, 2) }}</p>
        <p class="stat-desc">Total billed this month</p>
    </div>

    <div class="stat-card">
        <h3>Total Cost</h3>
        <p class="stat-number">LKR {{ number_format($monthlyCost, 2) }}</p>
        <p class="stat-desc">Parts + Labor this month</p>
    </div>
    
    <div class="stat-card highlight-card">
        <h3>Net Profit</h3>
        <p class="stat-number {{ $monthlyProfit >= 0 ? 'text-success' : 'text-danger' }}">
            LKR {{ number_format($monthlyProfit, 2) }}
        </p>
        <p class="stat-desc">Monthly earnings</p>
    </div>
</div>

<!-- Chart Section -->
<div class="card glass mt-4">
    <div class="card-header">
        <h3>Financial Performance (Last 7 Days)</h3>
    </div>
    <div class="card-body" style="height: 300px;">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Gradient for Revenue
        const gradientRevenue = ctx.createLinearGradient(0, 0, 0, 400);
        gradientRevenue.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Primary Blue
        gradientRevenue.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
        
        // Gradient for Profit
        const gradientProfit = ctx.createLinearGradient(0, 0, 0, 400);
        gradientProfit.addColorStop(0, 'rgba(34, 197, 94, 0.5)'); // Success Green
        gradientProfit.addColorStop(1, 'rgba(34, 197, 94, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Revenue',
                        data: {!! json_encode($chartRevenue) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: gradientRevenue,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Net Profit',
                        data: {!! json_encode($chartProfit) !!},
                        borderColor: '#22c55e',
                        backgroundColor: gradientProfit,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#94a3b8' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8' }
                    }
                }
            }
        });
    });
</script>

<style>
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .text-date {
        font-weight: 400;
        font-size: 0.9rem;
        background: rgba(255, 255, 255, 0.05);
        padding: 0.2rem 0.6rem;
        border-radius: 1rem;
        color: var(--text-main);
    }

    .text-success { color: var(--success); }
    .text-danger { color: var(--danger); }
    
    .highlight-card {
        background: linear-gradient(145deg, rgba(30, 41, 59, 0.7), rgba(59, 130, 246, 0.1));
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    /* Force 5 columns on desktop */
    @media (min-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(5, 1fr) !important;
        }
    }
</style>
@endsection
