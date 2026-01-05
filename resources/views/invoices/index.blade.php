@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Invoices</h2>
        <p class="text-muted">Manage repair invoices and direct sales</p>
    </div>
    <a href="{{ route('sales.create') }}" class="btn-primary">
        <i class="fas fa-cash-register" style="margin-right: 0.5rem;"></i> New Direct Sale
    </a>
</div>

<div class="card glass">
    <div class="toolbar-container">
        <!-- Search Form -->
        <form action="{{ route('invoices.index') }}" method="GET" class="search-form">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Invoice #, Customer..." class="search-input">
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="text-align: left;">Invoice #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th style="text-align: right;">Amount</th>
                    <th style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr class="clickable-row" onclick="window.location='{{ route('invoices.show', $invoice->id) }}'">
                    <td style="text-align: left; font-family: 'Courier New', monospace; font-weight: 600;">
                        INV-{{ preg_replace('/[^0-9]/', '', $invoice->repairJob->job_number) }}
                    </td>
                    <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="font-weight: 500;">{{ $invoice->repairJob->customer->name }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $invoice->repairJob->customer->phone ?? 'N/A' }}</div>
                    </td>
                    <td>
                        @if($invoice->repairJob->job_type === 'sale')
                            <span class="badge badge-admin">Direct Sale</span>
                        @else
                            <span class="badge badge-tech">Repair</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $invoice->status === 'paid' ? 'badge-success' : ($invoice->status === 'partial' ? 'badge-warning' : 'badge-danger') }}" 
                              style="{{ $invoice->status === 'paid' ? 'background: rgba(34, 197, 94, 0.15); color: #4ade80;' : '' }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td style="text-align: right; font-weight: 600;">
                        Rs. {{ number_format($invoice->total_amount, 2) }}
                    </td>
                    <td onclick="event.stopPropagation()">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="action-icon edit-icon" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding: 3rem;">
                        <i class="fas fa-file-invoice-dollar" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                        No invoices found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="padding: 1rem;">
        {{ $invoices->links() }}
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
