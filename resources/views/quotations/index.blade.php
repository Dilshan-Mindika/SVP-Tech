@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">QUOTATIONS LIST</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">All quotations</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('quotations', 'Quotations', [
                {name: 'Quotation Number', type: 'string', required: true, desc: 'Unique Quotation ID'},
                {name: 'Customer Phone', type: 'string', required: true, desc: 'Registered Customer Phone'},
                {name: 'Total', type: 'numeric', required: true, desc: 'Total quotation value in Rs.'},
                {name: 'Valid Until', type: 'date', required: true, desc: 'Expiration Date (YYYY-MM-DD)'},
                {name: 'Status', type: 'string', required: false, desc: 'Quotation status (active, converted, expired)'},
                {name: 'Notes', type: 'string', required: false, desc: 'Additional notes'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="exportExcel('quotations')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('quotations')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('quotations.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>CREATE QUOTATION</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Quotations</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Quotations generated</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Value</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">Rs. {{ number_format($stats['total_value'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Sum value of quotations</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #fbbf24; opacity: 0.15;">
                <i class="fa-solid fa-spinner"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Pending Status</span>
            <h3 class="text-xl font-extrabold text-amber-400 mt-1 mono-text">{{ $stats['pending_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Awaiting client decision</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-file-circle-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Converted Invoices</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">{{ $stats['converted_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Converted to active sales</span>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('quotations.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-550">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Quote No, Customer Name or Phone..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
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
                    <span class="text-slate-555 text-xs">to</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                </div>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                        <th class="py-3.5 px-6">Quote Number</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Valid Until</th>
                        <th class="py-3.5 px-6">Subtotal</th>
                        <th class="py-3.5 px-6">Tax</th>
                        <th class="py-3.5 px-6">Grand Total</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-center">Options</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($quotations as $quote)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-cyan-400">{{ $quote->quotation_number }}</td>
                            <td class="py-3.5 px-6">
                                <span class="font-bold text-slate-200 block">{{ $quote->customer_name }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $quote->customer_phone }}</span>
                            </td>
                            <td class="py-3.5 px-6 text-slate-400 font-medium">
                                {{ \Carbon\Carbon::parse($quote->valid_until)->format('Y-m-d') }}
                                @if(\Carbon\Carbon::parse($quote->valid_until)->isPast() && $quote->status !== 'accepted')
                                    <span class="text-rose-500 text-[9px] font-bold block">(Expired)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-slate-300 mono-text">Rs. {{ number_format($quote->subtotal, 2) }}</td>
                            <td class="py-3.5 px-6 text-slate-400 mono-text">Rs. {{ number_format($quote->tax, 2) }}</td>
                            <td class="py-3.5 px-6 text-slate-100 font-bold mono-text">Rs. {{ number_format($quote->total, 2) }}</td>
                            <td class="py-3.5 px-6">
                                @if($quote->status === 'accepted')
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Accepted
                                    </span>
                                @elseif($quote->status === 'expired' || \Carbon\Carbon::parse($quote->valid_until)->isPast())
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        Expired
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Active / Sent
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('quotations.show', $quote->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all border border-slate-800" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('quotations.print', $quote->id) }}" target="_blank" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all border border-slate-800" title="Print Quote">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <a href="{{ route('quotations.edit', $quote->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all border border-slate-800" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('quotations.destroy', $quote->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this quotation?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-rose-500 transition-all border border-slate-800" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    @if($quote->status !== 'accepted' && !\Carbon\Carbon::parse($quote->valid_until)->isPast())
                                        <form action="{{ route('quotations.convert', $quote->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 bg-cyan-500/10 hover:bg-cyan-505 text-cyan-400 hover:text-slate-950 rounded transition-all border border-slate-800" title="Convert to Invoice">
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-file-invoice-dollar text-2xl mb-2 block opacity-40"></i>
                                <span>No quotations found. Click "CREATE QUOTATION" to log a proposal.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($quotations->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $quotations->links() }}
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
