@extends('layouts.app')

@section('title', 'Customer Ledger - ' . $customer->name)

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>{{ $customer->name }} - Payment Ledger</h2>
        <p class="text-muted">History of invoices and payments</p>
    </div>
    <a href="{{ route('customers.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i> Back to Customers
    </a>
</div>

<!-- Financial Summary Cards -->
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <h3>Total Billed</h3>
        <p class="stat-number text-primary">LKR {{ number_format($totalBilled, 2) }}</p>
        <p class="stat-desc">Lifetime invoices value</p>
    </div>
    
    <div class="stat-card">
        <h3>Total Paid</h3>
        <p class="stat-number text-success">LKR {{ number_format($totalPaid, 2) }}</p>
        <p class="stat-desc">Lifetime payments received</p>
    </div>
    
    <div class="stat-card {{ $totalDue > 0 ? 'highlight-card' : '' }}">
        <h3>Outstanding Due</h3>
        <p class="stat-number {{ $totalDue > 0 ? 'text-danger' : 'text-success' }}">
            LKR {{ number_format($totalDue, 2) }}
        </p>
        <p class="stat-desc">Current balance to pay</p>
    </div>
</div>

<!-- Invoices List -->
<div class="card glass">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3>Invoice History</h3>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Invoice #</th>
                    <th>Job / Sale</th>
                    <th style="text-align: right;">Total Amount</th>
                    <th style="text-align: right;">Paid</th>
                    <th style="text-align: right;">Balance</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td class="table-date-muted">{{ $invoice->created_at->format('M d, Y') }}</td>
                    <td class="table-text-main">{{ 'INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div class="table-text-sub">
                            {{ $invoice->repairJob->job_number }}
                            @if($invoice->repairJob->job_type == 'sale')
                                <span class="badge badge-sales" style="font-size: 0.7rem; margin-left: 5px;">Sale</span>
                            @else
                                <span class="badge badge-jobs" style="font-size: 0.7rem; margin-left: 5px;">Repair</span>
                            @endif
                        </div>
                    </td>
                    <td style="text-align: right; font-weight: 500;">
                        LKR {{ number_format($invoice->total_amount, 2) }}
                    </td>
                    <td style="text-align: right; color: var(--success);">
                        LKR {{ number_format($invoice->paid_amount, 2) }}
                    </td>
                    <td style="text-align: right; font-weight: bold; color: {{ $invoice->balance_due > 0 ? 'var(--danger)' : 'var(--text-muted)' }}">
                        LKR {{ number_format($invoice->balance_due, 2) }}
                    </td>
                    <td style="text-align: center;">
                        <span class="badge status-{{ $invoice->status }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                        @if($invoice->status != 'paid' && $invoice->due_date < now())
                            <span class="badge status-cancelled" style="margin-left: 5px; font-size: 0.7rem;">Overdue</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                            View
                        </a>
                        @if($invoice->balance_due > 0)
                            <a href="{{ route('payments.create', $invoice->id) }}" class="btn-success" style="padding: 0.3rem 0.6rem; font-size: 0.8rem; margin-left: 5px; background: rgba(34, 197, 94, 0.2); color: #4ade80;">
                                Pay
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding: 2rem;">No invoicing history found for this customer.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
