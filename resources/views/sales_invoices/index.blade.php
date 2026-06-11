@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">INVOICES LIST</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">All sales invoices and history</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('invoices', 'Invoices', [
                {name: 'Invoice Number', type: 'string', required: true, desc: 'Unique invoice document number (must be unique)'},
                {name: 'Customer Phone', type: 'string', required: true, desc: 'Registered customer telephone number'},
                {name: 'Total Amount', type: 'numeric', required: true, desc: 'Total transaction amount in Rs.'},
                {name: 'Amount Paid', type: 'numeric', required: true, desc: 'Wages paid in Rs.'},
                {name: 'Discount', type: 'numeric', required: false, desc: 'Flat discount amount in Rs.'},
                {name: 'Payment Method', type: 'string', required: true, desc: 'Method used (cash, bank, koko, payzy)'},
                {name: 'Status', type: 'string', required: true, desc: 'Invoice status (paid, unpaid, partial, installment)'},
                {name: 'Due Date', type: 'date', required: false, desc: 'Due date in YYYY-MM-DD format'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="exportExcel('invoices')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('invoices')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('sales_invoices.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>CREATE INVOICE</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Invoices</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Matching search filters</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Sales</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">Rs. {{ number_format($stats['total_revenue'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Aggregated revenue value</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #f43f5e; opacity: 0.15;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Fully Unpaid</span>
            <h3 class="text-xl font-extrabold text-rose-500 mt-1 mono-text">{{ $stats['fully_unpaid'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Pending payments</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #f43f5e; opacity: 0.15;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Unpaid/Partial Balance</span>
            <h3 class="text-xl font-extrabold text-rose-500 mt-1 mono-text">Rs. {{ number_format($stats['unpaid_receivables'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Outstanding collections due</span>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('sales_invoices.index') }}" method="GET" class="flex flex-col gap-4">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <div class="flex-grow w-full md:max-w-md relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Invoice No, Customer Name or Phone..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <!-- Date Preset Filter -->
                    <select name="date_filter" id="date_filter_select" onchange="toggleCustomDates(); this.form.submit();" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        <option value="" {{ request('date_filter') == '' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="weekly" {{ request('date_filter') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ request('date_filter') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="annually" {{ request('date_filter') == 'annually' ? 'selected' : '' }}>Annually</option>
                        <option value="custom" {{ request('date_filter') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>

                    <!-- Custom Date inputs -->
                    <div id="custom_dates" class="{{ request('date_filter') == 'custom' ? 'flex' : 'hidden' }} items-center gap-2">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                        <span class="text-slate-500 text-xs">to</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                    </div>

                    <!-- Type Filter -->
                    <select name="type" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>All Invoices</option>
                        <option value="tax" {{ request('type') === 'tax' ? 'selected' : '' }}>Tax Invoices Only</option>
                        <option value="standard" {{ request('type') === 'standard' ? 'selected' : '' }}>Standard Invoices</option>
                    </select>

                    <!-- Payment Status Filter -->
                    <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Fully Paid</option>
                        <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="installment" {{ request('status') === 'installment' ? 'selected' : '' }}>Installments</option>
                        <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>

                    <!-- Payment Method Filter -->
                    <select name="payment_method" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        <option value="all" {{ request('payment_method') === 'all' || !request('payment_method') ? 'selected' : '' }}>All Methods</option>
                        <option value="Cash" {{ request('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Card" {{ request('payment_method') === 'Card' ? 'selected' : '' }}>Card</option>
                        <option value="Bank Transfer" {{ request('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Koko" {{ request('payment_method') === 'Koko' ? 'selected' : '' }}>Koko</option>
                        <option value="Payzy" {{ request('payment_method') === 'Payzy' ? 'selected' : '' }}>Payzy</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition-colors">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                        <th class="py-3.5 px-6">Invoice Number</th>
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">User</th>
                        <th class="py-3.5 px-6">Invoice Type</th>
                        <th class="py-3.5 px-6 text-center">Payment Method</th>
                        <th class="py-3.5 px-6 text-center">Payment Status</th>
                        <th class="py-3.5 px-6 text-right">Discount</th>
                        <th class="py-3.5 px-6 text-right">Total</th>
                        <th class="py-3.5 px-6 text-center">Options</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-cyan-400">{{ $inv->invoice_number }}</td>
                            <td class="py-3.5 px-6 text-slate-400">{{ $inv->created_at->format('Y-m-d H:i A') }}</td>
                            <td class="py-3.5 px-6">
                                @if($inv->customer)
                                    <span class="font-bold text-slate-200 block">{{ $inv->customer->name }}</span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $inv->customer->phone }}</span>
                                @else
                                    <span class="text-slate-500 font-semibold italic">Walk-in Customer</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-slate-300 font-medium">{{ $inv->user->name }}</td>
                            <td class="py-3.5 px-6">
                                <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold
                                    {{ $inv->is_tax_invoice ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                                    {{ $inv->is_tax_invoice ? 'Tax (VAT 15%)' : 'Standard' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center text-slate-300 font-medium">
                                <span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 border border-slate-700 text-slate-300 font-sans">
                                    {{ $inv->payment_method }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-center font-bold">
                                @if($inv->status === 'paid')
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Fully Paid</span>
                                @elseif($inv->status === 'partial')
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Partially Paid</span>
                                @elseif($inv->status === 'installment')
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">Installment</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Unpaid</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right text-rose-400 mono-text">-Rs. {{ number_format($inv->discount, 2) }}</td>
                            <td class="py-3.5 px-6 text-right text-slate-100 font-bold mono-text">Rs. {{ number_format($inv->total, 2) }}</td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('sales_invoices.show', $inv->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="View details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sales_invoices.print', $inv->id) }}" target="_blank" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="Print Invoice">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <a href="{{ route('sales_invoices.print', $inv->id) }}?download=1" target="_blank" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="Download PDF">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('sales_invoices.edit', $inv->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    @php
                                        $emailSubject = rawurlencode("Invoice #" . $inv->invoice_number . " - CLOUDTECH");
                                        $emailBody = rawurlencode("Hi,\n\nPlease find your invoice #" . $inv->invoice_number . " details here: " . route('sales_invoices.show', $inv->id) . "\n\nThank you for choosing CLOUDTECH Computer Store!");
                                        
                                        $whatsappText = rawurlencode("Hi, please find your invoice #" . $inv->invoice_number . " details here: " . route('sales_invoices.show', $inv->id));
                                    @endphp

                                    <a href="mailto:{{ $inv->customer ? $inv->customer->email : '' }}?subject={{ $emailSubject }}&body={{ $emailBody }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="Share via Email">
                                        <i class="fa-solid fa-envelope"></i>
                                    </a>
                                    <a href="https://api.whatsapp.com/send?text={{ $whatsappText }}" target="_blank" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-emerald-500 transition-all" title="Share via WhatsApp">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>

                                    <form action="{{ route('sales_invoices.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this invoice?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-rose-500 transition-all" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-receipt text-2xl mb-2 block opacity-40"></i>
                                <span>No invoices found. Process sales transactions at the terminal.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleCustomDates() {
    var select = document.getElementById('date_filter_select');
    var customDiv = document.getElementById('custom_dates');
    if (select.value === 'custom') {
        customDiv.classList.remove('hidden');
        customDiv.classList.add('flex');
    } else {
        customDiv.classList.add('hidden');
        customDiv.classList.remove('flex');
    }
}
</script>
@endsection
