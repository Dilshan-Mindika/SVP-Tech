@extends('layouts.app')

@section('title', 'Outstanding Invoices Report')

@section('content')
<div class="page-header print:hidden">
    <div class="header-content">
        <h2>Outstanding Invoices</h2>
        <p class="text-muted">Summary of unpaid balances and overdue payments</p>
    </div>
    <div class="flex gap-4" style="display: flex; gap: 1rem; align-items: center; white-space: nowrap;">
        {{-- Filter Dropdown --}}
        <form action="{{ route('reports.outstanding') }}" method="GET" style="display: flex; align-items: center; margin: 0;">
            <select name="customer_type" onchange="this.form.submit()" class="bg-gray-800 text-white border border-gray-600 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500 rounded-lg" style="margin-right: 0;">
                <option value="all" {{ $customerType == 'all' ? 'selected' : '' }}>All Customers</option>
                <option value="shop" {{ $customerType == 'shop' ? 'selected' : '' }}>Shops</option>
                <option value="normal" {{ $customerType == 'normal' ? 'selected' : '' }}>Individuals</option>
            </select>
        </form>

        <button onclick="window.print()" class="btn-primary" style="white-space: nowrap; display: flex; align-items: center;">
            <i class="fas fa-print mr-2"></i> Print Report
        </button>
    </div>
</div>

{{-- Summary Stats Cards (Screen Only) --}}
<div class="stats-grid print:hidden mb-8" style="grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
    <div class="stat-card">
        <h3>Total Outstanding</h3>
        <p class="stat-number text-danger">Rs. {{ number_format($totalOutstanding, 2) }}</p>
        <p class="stat-desc">Total uncollected revenue</p>
    </div>
    <div class="stat-card">
        <h3>Customers with Debt</h3>
        <p class="stat-number text-primary">{{ $customersWithDebtCount }}</p>
        <p class="stat-desc">Active debtors</p>
    </div>
    <div class="stat-card">
        <h3>Unpaid Invoices</h3>
        <p class="stat-number text-warning">{{ $totalOverdueInvoices }}</p>
        <p class="stat-desc">Total open invoices</p>
    </div>
</div>

<div class="report-content bg-white text-black p-8 rounded-lg shadow-lg print:shadow-none print:w-full print:p-0">
    {{-- Print Header --}}
    <div class="text-center mb-8 border-b-2 border-black pb-4 hidden print:block">
        <h1 class="text-3xl font-bold uppercase">SVP Technologies</h1>
        <p class="text-sm uppercase tracking-wide">Outstanding Invoices Report</p>
        <p class="text-xs text-gray-600 mt-1">Generated on: {{ now()->format('Y-m-d H:i') }} | Type: {{ ucfirst($customerType) }}</p>
    </div>

    {{-- Customer Cards --}}
    @forelse($customers as $customer)
        @php
            $invoices = $customer->repairJobs->flatMap->invoices->where('status', '!=', 'paid');
            $customerTotalOutstanding = $invoices->sum('balance_due');
            if ($customerTotalOutstanding <= 0) continue; 
        @endphp

        <div class="mb-8 break-inside-avoid border border-gray-200 rounded-lg overflow-hidden">
            {{-- Customer Header --}}
            <div class="bg-gray-100 p-4 flex justify-between items-center print:bg-gray-200">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $customer->name }}</h3>
                    <div class="text-sm text-gray-600 flex gap-4">
                        <span><i class="fas fa-phone mr-1"></i> {{ $customer->phone ?? 'N/A' }}</span>
                        @if($customer->type == 'shop')
                            <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-bold uppercase">Shop</span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <span class="block text-xs text-gray-500 uppercase font-bold">Total Due</span>
                    <span class="block text-xl font-bold text-red-600">Rs. {{ number_format($customerTotalOutstanding, 2) }}</span>
                </div>
            </div>
            
            {{-- Invoices Table --}}
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 border-b border-gray-200">
                    <tr>
                        <th class="py-2 px-4 font-semibold">Invoice #</th>
                        <th class="py-2 px-4 font-semibold">Date</th>
                        <th class="py-2 px-4 font-semibold">Job/Sale</th>
                        <th class="py-2 px-4 font-semibold text-right">Total</th>
                        <th class="py-2 px-4 font-semibold text-right">Paid</th>
                        <th class="py-2 px-4 font-semibold text-right">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 font-mono text-gray-700">INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-2 px-4 text-gray-600">{{ $invoice->created_at->format('Y-m-d') }}</td>
                        <td class="py-2 px-4 text-gray-600 text-xs">
                            {{ $invoice->repairJob->job_type == 'sale' ? 'Direct Sale' : 'Repair Job' }}
                        </td>
                        <td class="py-2 px-4 text-right font-medium text-gray-800">Rs. {{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="py-2 px-4 text-right text-green-600">{{ number_format($invoice->paid_amount, 2) }}</td>
                        <td class="py-2 px-4 text-right font-bold text-red-600">Rs. {{ number_format($invoice->balance_due, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="text-center py-12">
            <div class="inline-block p-4 rounded-full bg-green-100 text-green-600 mb-4">
                <i class="fas fa-check-circle text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">All Clear!</h3>
            <p class="text-gray-500 mt-2">No outstanding invoices found for the selected criteria.</p>
        </div>
    @endforelse

    {{-- Grand Total --}}
    @if($totalOutstanding > 0)
    <div class="mt-8 pt-6 border-t-2 border-black flex justify-between items-center bg-gray-50 p-6 rounded-lg print:bg-transparent print:p-0 print:border-t-2 print:border-black">
        <div>
            <span class="block text-2xl font-bold uppercase tracking-wider text-gray-800">Grand Total Outstanding</span>
            <span class="text-sm text-gray-500">{{ $customersWithDebtCount }} Customers | {{ $totalOverdueInvoices }} Invoices</span>
        </div>
        <span class="text-3xl font-bold text-red-600">Rs. {{ number_format($totalOutstanding, 2) }}</span>
    </div>
    @endif
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .report-content, .report-content * {
            visibility: visible;
        }
        .report-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            background: white !important;
            color: black !important;
            box-shadow: none !important;
        }
        .print\:hidden {
            display: none !important;
        }
        .print\:block {
            display: block !important;
        }
        .print\:bg-gray-200 {
            background-color: #e5e7eb !important;
            -webkit-print-color-adjust: exact;
        }
        .stat-card {
            display: none;
        }
        /* Ensure table headers render nicely */
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
    }
</style>
@endsection
