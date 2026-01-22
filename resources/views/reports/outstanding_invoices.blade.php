@extends('layouts.app')

@section('title', 'Outstanding Invoices Report')

@section('content')
<div class="page-header print:hidden">
    <div class="header-content">
        <h2>Outstanding Invoices</h2>
        <p class="text-muted">Summary of unpaid balances and overdue payments</p>
    </div>
    <div class="toolbar-container print:hidden">
        <!-- Advanced Filter Toolbar -->
        <form action="{{ route('reports.outstanding') }}" method="GET" class="filter-toolbar">
            
            <!-- Search -->
            <div class="search-group">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Customer..." class="search-input">
            </div>

            <!-- Filters Group -->
            <div class="filter-group">
                <!-- Customer Type Filter -->
                <div class="select-wrapper">
                    <i class="fas fa-users select-icon"></i>
                    <select name="customer_type" class="filter-select">
                        <option value="all">All Customers</option>
                        <option value="shop" {{ request('customer_type') == 'shop' ? 'selected' : '' }}>Shops</option>
                        <option value="normal" {{ request('customer_type') == 'normal' ? 'selected' : '' }}>Individuals</option>
                    </select>
                </div>

                <!-- Actions -->
                <button type="submit" class="btn-filter" title="Apply Filters">
                    <i class="fas fa-filter"></i> <span>Filter</span>
                </button>
                
                @if(request('search') || (request('customer_type') && request('customer_type') != 'all'))
                    <a href="{{ route('reports.outstanding') }}" class="btn-clear" title="Clear Filters">
                        <i class="fas fa-times"></i>
                    </a>
                @endif

                <!-- Print Button (Moved inside toolbar acting as a tool) -->
                <button type="button" onclick="window.print()" class="btn-secondary" style="height: 42px; display: flex; align-items: center; gap: 0.5rem; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: #60a5fa;">
                    <i class="fas fa-print"></i> <span>Print</span>
                </button>
            </div>
        </form>
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
    <div class="print-header hidden print:block">
        <div>
            <h1>Cloud Tech</h1>
            <div class="company-meta">
                90/1, Diddeniya, Hanwella<br>
                0785315902 • cloudtech.lk
            </div>
        </div>
        <div class="report-meta">
            <h2>Outstanding Report</h2>
            <table class="meta-table">
                <tr>
                    <td>Date:</td>
                    <td><strong>{{ now()->format('Y-m-d') }}</strong></td>
                </tr>
                <tr>
                    <td>Type:</td>
                    <td><strong>{{ ucfirst($customerType) }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Customer Cards --}}
    @forelse($customers as $customer)
        @php
            $invoices = $customer->repairJobs->flatMap->invoices->where('status', '!=', 'paid');
            $customerTotalOutstanding = $invoices->sum('balance_due');
            if ($customerTotalOutstanding <= 0) continue; 
        @endphp

        <div class="mb-6 break-inside-avoid print:mb-4">
            {{-- Customer Header --}}
            <div class="bg-gray-50 border border-gray-200 rounded-t-lg p-3 flex justify-between items-center print:border-black print:bg-gray-100 print:py-2">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-bold text-gray-900 print:text-base">{{ $customer->name }}</h3>
                    @if($customer->type == 'shop')
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-bold uppercase border border-blue-200 print:text-black print:border-black print:bg-transparent">Shop</span>
                    @endif
                </div>
                <div class="text-right flex items-center gap-4">
                    <span class="text-sm text-gray-500 print:text-black"><i class="fas fa-phone mr-1"></i> {{ $customer->phone ?? 'N/A' }}</span>
                    <div class="pl-4 border-l border-gray-300">
                        <span class="text-xs text-gray-500 uppercase font-bold mr-2">Total Due</span>
                        <span class="text-lg font-bold text-red-600 print:text-black">Rs. {{ number_format($customerTotalOutstanding, 2) }}</span>
                    </div>
                </div>
            </div>
            
            {{-- Invoices Table --}}
            <div class="border-x border-b border-gray-200 rounded-b-lg print:border-black">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white text-gray-600 border-b border-gray-200 print:text-black print:border-black">
                        <tr>
                            <th class="py-2 px-4 font-semibold w-24">Invoice No</th>
                            <th class="py-2 px-4 font-semibold w-24">Date</th>
                            <th class="py-2 px-4 font-semibold">Type</th>
                            <th class="py-2 px-4 font-semibold text-right w-32">Total</th>
                            <th class="py-2 px-4 font-semibold text-right w-32">Paid</th>
                            <th class="py-2 px-4 font-semibold text-right w-32">Balance</th>
                            <th class="py-2 px-4 font-semibold text-center w-20 print:hidden">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 print:divide-gray-300">
                        @foreach($invoices as $invoice)
                        <tr class="hover:bg-gray-50 print:hover:bg-transparent">
                            <td class="py-1.5 px-4 font-mono text-gray-700 font-medium print:text-black">INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-1.5 px-4 text-gray-600 print:text-black">{{ $invoice->created_at->format('Y-m-d') }}</td>
                            <td class="py-1.5 px-4 text-gray-600 text-xs uppercase tracking-wide print:text-black">
                                {{ $invoice->repairJob->job_type == 'sale' ? 'Direct Sale' : 'Repair Job' }}
                            </td>
                            <td class="py-1.5 px-4 text-right font-medium text-gray-800 print:text-black">{{ number_format($invoice->total_amount, 2) }}</td>
                            <td class="py-1.5 px-4 text-right text-green-600 print:text-black">{{ number_format($invoice->paid_amount, 2) }}</td>
                            <td class="py-1.5 px-4 text-right font-bold text-red-600 print:text-black">{{ number_format($invoice->balance_due, 2) }}</td>
                            <td class="py-1.5 px-4 text-center print:hidden">
                                <a href="{{ route('payments.create', $invoice->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600 hover:bg-green-200 transition-colors" title="Settle Invoice">
                                    <i class="fas fa-hand-holding-usd text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
    <div class="mt-8 pt-6 border-t-2 border-black flex justify-between items-center bg-gray-50 p-6 rounded-lg print:break-inside-avoid print:bg-transparent print:p-0 print:pt-4 print:mt-4 print:border-black">
        <div>
            <span class="block text-2xl font-bold uppercase tracking-wider text-gray-800 print:text-black print:text-xl">Grand Total Outstanding</span>
            <span class="text-sm text-gray-500 print:text-black">{{ $customersWithDebtCount }} Customers | {{ $totalOverdueInvoices }} Invoices</span>
        </div>
        <span class="text-3xl font-bold text-red-600 print:text-black print:text-2xl">Rs. {{ number_format($totalOutstanding, 2) }}</span>
    </div>
    @endif
    
    {{-- Print Footer --}}
    <div class="print-footer hidden print:block fixed bottom-0 left-0 w-full text-center text-xs text-gray-500 border-t border-gray-300 pt-2">
        <p>This is a computer-generated document and requires no signature. | Page <span class="page-number"></span></p>
    </div>
</div>

{{-- Standard Screen View (Unchanged) --}}
<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 12mm; /* Slightly tighter margins for more content */
        }

        /* -----------------------------------------------------------------
           RESET & BASE
           ----------------------------------------------------------------- */
        body {
            background-color: white !important;
            font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif !important;
            color: #111 !important;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.3;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Visibility Control */
        body * { visibility: hidden; }
        .report-content, .report-content * { visibility: visible; }

        .report-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 10mm;
            background: white !important;
            z-index: 9999;
        }

        /* -----------------------------------------------------------------
           HEADER SECTION
           ----------------------------------------------------------------- */
        .print-header {
            margin-bottom: 30px !important;
            padding-bottom: 20px !important;
            border-bottom: 3px solid #000 !important; /* Bold anchor line */
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-end !important;
        }

        .print-header h1 {
            font-size: 26pt !important;
            font-weight: 900 !important;
            letter-spacing: -1px !important;
            text-transform: uppercase !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        .print-header .company-meta {
            font-size: 8pt !important;
            color: #555 !important;
            margin-top: 5px !important;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .print-header .report-meta {
            text-align: right !important;
        }
        
        .print-header h2 {
            font-size: 16pt !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            margin: 0 0 5px 0 !important;
        }

        .print-header .meta-table {
            font-size: 9pt !important;
            border-collapse: collapse;
        }
        .print-header .meta-table td {
            padding-left: 15px !important;
            text-align: right !important;
        }

        /* -----------------------------------------------------------------
           CUSTOMER SECTION (The "Statement" Look)
           ----------------------------------------------------------------- */
        .break-inside-avoid {
            page-break-inside: avoid !important;
            margin-bottom: 35px !important; /* Generous spacing between customers */
            border: none !important; /* No boxes */
            background: transparent !important;
        }

        /* Customer Header */
        .bg-gray-50, .border, .rounded-t-lg, .rounded-b-lg {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
        }

        /* The Name & Total Row */
        .mb-6 .flex.justify-between {
            border-bottom: 2px solid #000 !important; /* Strong divider */
            padding-bottom: 5px !important;
            margin-bottom: 10px !important;
            align-items: flex-end !important;
        }

        h3.text-lg {
            font-size: 14pt !important;
            font-weight: 800 !important;
            color: #000 !important;
            margin: 0 !important;
        }

        .text-red-600 {
            color: #000 !important; /* Black for professional print */
            font-size: 14pt !important;
            font-weight: 700 !important;
        }
        
        /* Helper to hide phone icon for cleaner print */
        .fa-phone { display: none !important; } 

        /* -----------------------------------------------------------------
           TABLE STYLES
           ----------------------------------------------------------------- */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        thead {
            border-bottom: 1px solid #444 !important;
        }

        th {
            font-size: 7.5pt !important;
            text-transform: uppercase !important;
            color: #666 !important;
            font-weight: 600 !important;
            padding: 4px 0 !important;
            text-align: left !important;
            letter-spacing: 0.5px;
        }
        
        /* Alignments */
        th.text-right, td.text-right { text-align: right !important; }

        tbody tr {
            border-bottom: 1px dotted #ccc !important; /* Lightweight separators */
        }

        td {
            font-size: 9.5pt !important;
            padding: 6px 0 !important;
            color: #222 !important;
        }
        
        /* Specific column tweaks */
        td.font-mono { font-family: 'Courier New', monospace !important; font-size: 9pt !important; }

        /* -----------------------------------------------------------------
           GRAND TOTAL
           ----------------------------------------------------------------- */
        .mt-8.pt-6 {
            margin-top: 40px !important;
            padding-top: 15px !important;
            border-top: 4px double #000 !important; /* Classic accounting double line */
            background: transparent !important;
        }
        
        .block.text-2xl { font-size: 14pt !important; text-transform: uppercase; }
        .text-3xl { font-size: 18pt !important; }

        /* -----------------------------------------------------------------
           FOOTER
           ----------------------------------------------------------------- */
        .print-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            font-size: 7pt !important;
            color: #888 !important;
            border-top: 1px solid #ddd !important;
            padding-top: 5px !important;
            text-align: center !important;
        }
        .page-number::after { content: counter(page); }

        /* Utilities Override */
        .hidden, .print\:hidden, .stat-card { display: none !important; }
        .print\:block { display: block !important; }
        .text-xs { font-size: 8pt !important; }
    }
</style>
@endsection
