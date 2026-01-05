@extends('layouts.app')

@section('title', 'Outstanding Invoices Report')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="print:hidden flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Outstanding Invoices Report</h1>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-print mr-2"></i> Print Report
        </button>
    </div>

    <!-- Filter Form -->
    <div class="print:hidden bg-gray-800 rounded-lg p-4 mb-8">
        <form action="{{ route('reports.outstanding') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-gray-400 mb-2">Customer Type</label>
                <select name="customer_type" class="bg-gray-700 text-white rounded px-3 py-2 border border-gray-600 focus:outline-none focus:border-blue-500">
                    <option value="all" {{ $customerType == 'all' ? 'selected' : '' }}>All Customers</option>
                    <option value="shop" {{ $customerType == 'shop' ? 'selected' : '' }}>Shops</option>
                    <option value="normal" {{ $customerType == 'normal' ? 'selected' : '' }}>Normal Customers</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Filter</button>
            </div>
        </form>
    </div>

    <!-- Report Content -->
    <div class="report-content bg-white text-black p-8 rounded-lg shadow-lg print:shadow-none print:w-full">
        <div class="text-center mb-8 border-b-2 border-black pb-4">
            <h1 class="text-3xl font-bold uppercase">SVP Technologies</h1>
            <p class="text-sm">Outstanding Invoices Report</p>
            <p class="text-xs text-gray-600">Generated on: {{ now()->format('Y-m-d H:i') }}</p>
            @if($customerType !== 'all')
            <p class="text-sm font-semibold mt-2 uppercase">Type: {{ $customerType }}</p>
            @endif
        </div>

        @php
            $grandTotalOutstanding = 0;
        @endphp

        @forelse($customers as $customer)
            @php
                $customerTotalOutstanding = $customer->repairJobs->flatMap->invoices->where('status', '!=', 'paid')->sum('balance_due');
                if ($customerTotalOutstanding <= 0) continue; // Skip if no meaningful debt
                $grandTotalOutstanding += $customerTotalOutstanding;
            @endphp

            <div class="mb-8 break-inside-avoid">
                <div class="flex justify-between items-baseline mb-2 bg-gray-100 p-2 rounded print:bg-gray-200">
                    <h3 class="text-lg font-bold">{{ $customer->name }} <span class="text-xs font-normal text-gray-600">({{ ucfirst($customer->type) }})</span></h3>
                    <span class="font-bold text-red-600">Total Due: Rs. {{ number_format($customerTotalOutstanding, 2) }}</span>
                </div>
                
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-400">
                            <th class="py-1">Invoice #</th>
                            <th class="py-1">Date</th>
                            <th class="py-1 text-right">Total</th>
                            <th class="py-1 text-right">Paid</th>
                            <th class="py-1 text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer->repairJobs->flatMap->invoices->where('status', '!=', 'paid') as $invoice)
                        <tr class="border-b border-gray-200">
                            <td class="py-1">INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-1">{{ $invoice->created_at->format('Y-m-d') }}</td>
                            <td class="py-1 text-right">Rs. {{ number_format($invoice->total_amount, 2) }}</td>
                            <td class="py-1 text-right text-green-600">{{ number_format($invoice->paid_amount, 2) }}</td>
                            <td class="py-1 text-right font-bold text-red-600">Rs. {{ number_format($invoice->balance_due, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                No outstanding invoices found for the selected criteria.
            </div>
        @endforelse

        <div class="mt-8 pt-4 border-t-2 border-black flex justify-between items-center text-xl font-bold">
            <span>GRAND TOTAL OUTSTANDING</span>
            <span>Rs. {{ number_format($grandTotalOutstanding, 2) }}</span>
        </div>
    </div>
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
            box-shadow: none;
        }
        .print\:hidden {
            display: none !important;
        }
        /* Restore background colors for print if possible */
        .print\:bg-gray-200 {
            background-color: #e5e7eb !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection
