@extends('layouts.app')

@section('title', 'Financial Reports')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Business Reports</h2>
        <p class="text-muted">Financial Overview & Analytics</p>
    </div>
    <button onclick="window.print()" class="btn-primary">Print Report</button>
</div>

<div class="reports-grid">
    <!-- Revenue Section -->
    <div class="report-card glass highlight-blue">
        <h3>Revenue Overview</h3>
        <div class="stat-row">
            <span>Today</span>
            <span class="value">${{ number_format($dailyRevenue, 2) }}</span>
        </div>
        <div class="stat-row">
            <span>This Week</span>
            <span class="value">${{ number_format($weeklyRevenue, 2) }}</span>
        </div>
        <div class="stat-row main">
            <span>This Month</span>
            <span class="value">${{ number_format($monthlyRevenue, 2) }}</span>
        </div>
    </div>

    <!-- Profit Section -->
    <div class="report-card glass highlight-green">
        <h3>Profit Analysis</h3>
        <div class="stat-row">
            <span>Daily Profit</span>
            <span class="value">${{ number_format($dailyProfit, 2) }}</span>
        </div>
        <div class="stat-row main">
            <span>Net Profit (Mo)</span>
            <span class="value">${{ number_format($monthlyProfit, 2) }}</span>
        </div>
        <p class="desc">Calculated as (Total - Parts Cost - Labor Cost)</p>
    </div>

    <!-- Operational Section -->
    <div class="report-card glass highlight-purple">
        <h3>Operations</h3>
        <div class="stat-row">
            <span>Completed Today</span>
            <span class="value">{{ $jobsCompletedToday }} Jobs</span>
        </div>
        <div class="stat-row">
            <span>Active Queue</span>
            <span class="value">{{ $activeJobs }} Jobs</span>
        </div>
    </div>
</div>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .report-card {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        transition: transform 0.3s ease;
    }

    .report-card:hover {
        transform: translateY(-5px);
    }

    .report-card h3 {
        font-size: 1.2rem;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-glass);
        padding-bottom: 1rem;
        margin-bottom: 0.5rem;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1rem;
    }

    .stat-row.main {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 1rem;
        color: #fff;
    }

    .value {
        font-family: monospace;
        font-weight: 600;
    }

    /* Highlights */
    .highlight-blue .value { color: #60a5fa; }
    .highlight-green .value { color: #4ade80; }
    .highlight-purple .value { color: #c084fc; }

    .desc {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-style: italic;
    }

    @media print {
        .page-header button { display: none; }
        .glass { border: 1px solid #ccc; background: #fff; color: #000; box-shadow: none; }
        body { background: #fff; color: #000; }
        .sidebar { display: none; }
        .main-content { margin: 0; padding: 0; width: 100%; background: none; }
    }
</style>
@endsection
