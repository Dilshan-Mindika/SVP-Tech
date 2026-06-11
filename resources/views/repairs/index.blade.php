@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">REPAIR CENTER</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Manage repair jobs, assign technicians, and track repair status</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('repairs', 'Repairs', [
                {name: 'Job No', type: 'string', required: false, desc: 'Repair job reference ID (auto-generated if omitted)'},
                {name: 'Customer Name', type: 'string', required: true, desc: 'Customer name'},
                {name: 'Customer Phone', type: 'string', required: true, desc: 'Customer phone number'},
                {name: 'Customer Email', type: 'string', required: false, desc: 'Customer email'},
                {name: 'Device Model', type: 'string', required: true, desc: 'Brand and model of device'},
                {name: 'Device Serial', type: 'string', required: false, desc: 'Device manufacturer serial number'},
                {name: 'Issue Description', type: 'string', required: true, desc: 'Observed hardware issues and diagnostic requests'},
                {name: 'Estimate Cost', type: 'numeric', required: true, desc: 'Estimated repair cost in Rs.'},
                {name: 'Final Cost', type: 'numeric', required: false, desc: 'Actual billed amount in Rs.'},
                {name: 'Status', type: 'string', required: false, desc: 'Job status (pending, in-progress, completed, cancelled)'},
                {name: 'Notes', type: 'string', required: false, desc: 'Internal service notes / technician comments'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="exportExcel('repairs')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('repairs')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('appointments.index') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-1.5 border border-slate-700">
                <i class="fa-solid fa-calendar-check"></i>
                <span>APPOINTMENTS</span>
            </a>
            <a href="{{ route('repairs.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>ADD NEW REPAIR</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Repair Jobs</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Total repair entries</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #fbbf24; opacity: 0.15;">
                <i class="fa-solid fa-hourglass-start"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Pending Intake</span>
            <h3 class="text-xl font-extrabold text-amber-400 mt-1 mono-text">{{ $stats['pending_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Awaiting inspection</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-gears"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">In Progress</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['in_progress_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Currently on tech benches</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Completed & Collected</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">{{ $stats['completed_collected'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Ready or returned to customer</span>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('repairs.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Job #, Customer, Device or Phone..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
            </div>

            <div class="flex flex-wrap gap-3 w-full md:w-auto">
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
                    <span class="text-slate-550 text-xs">to</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                </div>

                <!-- Status Filter -->
                <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    <option value="all">All Statuses</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
                    <option value="diagnosing" {{ request('status') === 'diagnosing' ? 'selected' : '' }}>Diagnosing</option>
                    <option value="repairing" {{ request('status') === 'repairing' ? 'selected' : '' }}>Repairing</option>
                    <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready for Pickup</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered & Paid</option>
                </select>

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
                        <th class="py-3.5 px-6">Job Number</th>
                        <th class="py-3.5 px-6">Customer Details</th>
                        <th class="py-3.5 px-6">Device Details</th>
                        <th class="py-3.5 px-6">Technician</th>
                        <th class="py-3.5 px-6 text-right">Estimate Cost</th>
                        <th class="py-3.5 px-6 text-right">Final Bill</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($repairs as $r)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-cyan-400 tracking-wider uppercase mono-text text-sm">
                                {{ $r->repair_job_no }}
                            </td>
                            <td class="py-3.5 px-6 font-bold text-slate-200">
                                {{ $r->customer_name }}
                                <span class="text-[10px] text-slate-500 block font-mono mt-0.5">{{ $r->customer_phone }}</span>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="text-slate-300 font-semibold block">{{ $r->device_model }}</span>
                                <span class="text-[10px] text-slate-550 block font-mono mt-0.5">S/N: {{ $r->device_serial ?: 'Not Serialized' }}</span>
                            </td>
                            <td class="py-3.5 px-6 text-slate-400">
                                @if($r->technician)
                                    <span class="font-semibold text-slate-300">{{ $r->technician->name }}</span>
                                @else
                                    <span class="text-slate-650 italic text-[10px] uppercase font-bold">UNASSIGNED</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right text-slate-400 mono-text">Rs. {{ number_format($r->estimate_cost, 2) }}</td>
                            <td class="py-3.5 px-6 text-right text-cyan-400 font-bold mono-text">Rs. {{ number_format($r->final_cost, 2) }}</td>
                            <td class="py-3.5 px-6 text-center">
                                @if($r->status === 'received')
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-slate-550/10 text-slate-400 border border-slate-500/20">
                                        Received
                                    </span>
                                @elseif($r->status === 'diagnosing')
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-violet-500/10 text-violet-400 border border-violet-500/20">
                                        Diagnosing
                                    </span>
                                @elseif($r->status === 'repairing')
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Repairing
                                    </span>
                                @elseif($r->status === 'ready')
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_8px_rgba(16,185,129,0.1)]">
                                        Ready
                                    </span>
                                @elseif($r->status === 'delivered')
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                        Delivered
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-slate-800 text-slate-400">
                                        {{ $r->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('repairs.show', $r->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 hover:text-cyan-400 rounded transition-all" title="View details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('repairs.edit', $r->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 hover:text-cyan-400 rounded transition-all" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('repairs.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this repair job?')" class="inline">
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
                            <td colspan="8" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-screwdriver-wrench text-2xl mb-2 block opacity-40"></i>
                                <span>No repairs found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($repairs->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $repairs->links() }}
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
