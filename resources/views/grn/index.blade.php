@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">GOODS RECEIVED</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">All goods received</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('grns', 'Goods Received', [
                {name: 'GRN Number', type: 'string', required: true, desc: 'Unique Goods Received Note ID'},
                {name: 'Supplier Phone', type: 'string', required: true, desc: 'Registered Supplier Phone'},
                {name: 'Total Amount', type: 'numeric', required: true, desc: 'Total inventory bill value in Rs.'},
                {name: 'Date Received', type: 'date', required: true, desc: 'Received date (YYYY-MM-DD)'},
                {name: 'Notes', type: 'string', required: false, desc: 'Purchase details or notes'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="exportExcel('grns')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('grns')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('suppliers.index') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-705 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-1.5 border border-slate-700">
                <i class="fa-solid fa-handshake"></i>
                <span>SUPPLIERS</span>
            </a>
            <a href="{{ route('grn.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>NEW GOODS RECEIVED</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total GRNs</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Total receipts matching</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Purchase Value</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">Rs. {{ number_format($stats['total_value'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Total value of goods received</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Received Units</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['received_items'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Total item count received</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #94a3b8; opacity: 0.15;">
                <i class="fa-solid fa-building-flag"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Active Suppliers</span>
            <h3 class="text-xl font-extrabold text-slate-200 mt-1 mono-text">{{ $stats['unique_suppliers'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Suppliers supplying current GRNs</span>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('grn.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by GRN Number or Supplier Company..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
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
                        <th class="py-3.5 px-6">GRN Number</th>
                        <th class="py-3.5 px-6">Supplier</th>
                        <th class="py-3.5 px-6">Date Received</th>
                        <th class="py-3.5 px-6">User</th>
                        <th class="py-3.5 px-6 text-right">Total Cost</th>
                        <th class="py-3.5 px-6">Notes</th>
                        <th class="py-3.5 px-6 text-center">Options</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($grns as $g)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-cyan-400 tracking-wider uppercase mono-text text-sm">
                                {{ $g->grn_number }}
                            </td>
                            <td class="py-3.5 px-6 font-bold text-slate-200">
                                {{ $g->supplier->name }}
                                <span class="text-[10px] text-slate-500 block mt-0.5">{{ $g->supplier->company_name }}</span>
                            </td>
                            <td class="py-3.5 px-6 text-slate-300">
                                {{ \Carbon\Carbon::parse($g->date_received)->format('Y-m-d') }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-400">
                                {{ $g->receiver->name }}
                            </td>
                            <td class="py-3.5 px-6 text-right text-cyan-400 font-bold mono-text text-sm">
                                Rs. {{ number_format($g->total_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-550 italic max-w-xs truncate">
                                {{ $g->notes ?: 'No records attached.' }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('grn.show', $g->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all border border-slate-800 font-bold" title="View">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('grn.edit', $g->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all border border-slate-800 font-bold" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('grn.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this GRN?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-rose-500 transition-all border border-slate-800 font-bold" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-truck-ramp-box text-2xl mb-2 block opacity-40"></i>
                                <span>No GRNs logged in this billing cycles.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($grns->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $grns->links() }}
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
