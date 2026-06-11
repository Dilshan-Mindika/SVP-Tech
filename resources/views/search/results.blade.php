@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">SEARCH RESULTS</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">
                Showing matching records for query: <span class="text-cyan-400 font-bold">"{{ $query }}"</span>
            </p>
        </div>
    </div>

    @php
        $totalResults = $products->count() + $customers->count() + $invoices->count() + $quotations->count() + $repairs->count() + $suppliers->count();
    @endphp

    @if($totalResults === 0)
        <!-- No Results Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-rose-500/10 text-rose-500 text-2xl mb-4">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-100">No results found</h3>
            <p class="text-slate-400 text-sm mt-2 max-w-md mx-auto">
                We couldn't find anything matching your search term. Try checking your spelling or searching for a different keyword.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            <!-- Products Section -->
            @if($products->isNotEmpty())
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <h3 class="font-bold text-slate-100">Products ({{ $products->count() }})</h3>
                        </div>
                        <a href="{{ route('products.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold uppercase tracking-wider">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-800/80">
                                    <th class="py-2 font-semibold">Product Name</th>
                                    <th class="py-2 font-semibold">SKU</th>
                                    <th class="py-2 font-semibold">Brand</th>
                                    <th class="py-2 font-semibold text-right">Price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @foreach($products as $product)
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="py-3 font-medium text-slate-200">
                                            <a href="{{ route('products.edit', $product->id) }}" class="hover:text-cyan-400 transition-colors">{{ $product->name }}</a>
                                        </td>
                                        <td class="py-3 text-slate-400 mono-text">{{ $product->sku }}</td>
                                        <td class="py-3 text-slate-400">{{ $product->brand }}</td>
                                        <td class="py-3 text-slate-200 text-right mono-text font-bold">Rs. {{ number_format($product->price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Invoices Section -->
            @if($invoices->isNotEmpty())
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <h3 class="font-bold text-slate-100">Invoices ({{ $invoices->count() }})</h3>
                        </div>
                        <a href="{{ route('invoices.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold uppercase tracking-wider">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-800/80">
                                    <th class="py-2 font-semibold">Invoice No.</th>
                                    <th class="py-2 font-semibold">Customer</th>
                                    <th class="py-2 font-semibold">Date</th>
                                    <th class="py-2 font-semibold text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @foreach($invoices as $invoice)
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="py-3 font-medium text-slate-200">
                                            <a href="{{ route('invoices.show', $invoice->id) }}" class="hover:text-cyan-400 transition-colors mono-text">{{ $invoice->invoice_number }}</a>
                                        </td>
                                        <td class="py-3 text-slate-400">{{ $invoice->customer->name ?? 'Guest Customer' }}</td>
                                        <td class="py-3 text-slate-400">{{ $invoice->created_at->format('Y-m-d') }}</td>
                                        <td class="py-3 text-slate-200 text-right mono-text font-bold">Rs. {{ number_format($invoice->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Customers Section -->
            @if($customers->isNotEmpty())
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <h3 class="font-bold text-slate-100">Customers ({{ $customers->count() }})</h3>
                        </div>
                        <a href="{{ route('customers.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold uppercase tracking-wider">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-800/80">
                                    <th class="py-2 font-semibold">Name</th>
                                    <th class="py-2 font-semibold">Mobile</th>
                                    <th class="py-2 font-semibold">Email</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @foreach($customers as $customer)
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="py-3 font-medium text-slate-200">
                                            <a href="{{ route('customers.show', $customer->id) }}" class="hover:text-cyan-400 transition-colors">{{ $customer->name }}</a>
                                        </td>
                                        <td class="py-3 text-slate-400 mono-text">{{ $customer->phone ?? 'N/A' }}</td>
                                        <td class="py-3 text-slate-400">{{ $customer->email ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Quotations Section -->
            @if($quotations->isNotEmpty())
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <h3 class="font-bold text-slate-100">Quotations ({{ $quotations->count() }})</h3>
                        </div>
                        <a href="{{ route('quotations.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold uppercase tracking-wider">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-800/80">
                                    <th class="py-2 font-semibold">Quotation No.</th>
                                    <th class="py-2 font-semibold">Customer</th>
                                    <th class="py-2 font-semibold">Date</th>
                                    <th class="py-2 font-semibold text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @foreach($quotations as $quotation)
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="py-3 font-medium text-slate-200">
                                            <a href="{{ route('quotations.show', $quotation->id) }}" class="hover:text-cyan-400 transition-colors mono-text">{{ $quotation->quotation_number }}</a>
                                        </td>
                                        <td class="py-3 text-slate-400">{{ $quotation->customer_name }}</td>
                                        <td class="py-3 text-slate-400">{{ $quotation->created_at->format('Y-m-d') }}</td>
                                        <td class="py-3 text-slate-200 text-right mono-text font-bold">Rs. {{ number_format($quotation->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Repairs Section -->
            @if($repairs->isNotEmpty())
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                                <i class="fa-solid fa-screwdriver-wrench"></i>
                            </div>
                            <h3 class="font-bold text-slate-100">Repairs ({{ $repairs->count() }})</h3>
                        </div>
                        <a href="{{ route('repairs.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold uppercase tracking-wider">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-800/80">
                                    <th class="py-2 font-semibold">Job No.</th>
                                    <th class="py-2 font-semibold">Customer</th>
                                    <th class="py-2 font-semibold">Device</th>
                                    <th class="py-2 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @foreach($repairs as $repair)
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="py-3 font-medium text-slate-200">
                                            <a href="{{ route('repairs.show', $repair->id) }}" class="hover:text-cyan-400 transition-colors mono-text">{{ $repair->repair_job_no }}</a>
                                        </td>
                                        <td class="py-3 text-slate-400">{{ $repair->customer_name }}</td>
                                        <td class="py-3 text-slate-400">{{ $repair->device_model }}</td>
                                        <td class="py-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $repair->status === 'completed' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                                {{ $repair->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Suppliers Section -->
            @if($suppliers->isNotEmpty())
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                                <i class="fa-solid fa-handshake"></i>
                            </div>
                            <h3 class="font-bold text-slate-100">Suppliers ({{ $suppliers->count() }})</h3>
                        </div>
                        <a href="{{ route('suppliers.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold uppercase tracking-wider">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-800/80">
                                    <th class="py-2 font-semibold">Name</th>
                                    <th class="py-2 font-semibold">Company</th>
                                    <th class="py-2 font-semibold">Phone</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @foreach($suppliers as $supplier)
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="py-3 font-medium text-slate-200">
                                            <a href="{{ route('suppliers.index') }}" class="hover:text-cyan-400 transition-colors">{{ $supplier->name }}</a>
                                        </td>
                                        <td class="py-3 text-slate-400">{{ $supplier->company_name }}</td>
                                        <td class="py-3 text-slate-400 mono-text">{{ $supplier->phone }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    @endif
</div>
@endsection
