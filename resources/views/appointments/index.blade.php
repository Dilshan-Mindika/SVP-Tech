@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">APPOINTMENTS</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Manage service appointments and customer consultations</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('appointments', 'Appointments', [
                {name: 'Appointment No', type: 'string', required: false, desc: 'Reference code (auto-generated if omitted)'},
                {name: 'Customer Name', type: 'string', required: true, desc: 'Customer full name'},
                {name: 'Customer Phone', type: 'string', required: true, desc: 'Customer contact phone'},
                {name: 'Customer Email', type: 'string', required: false, desc: 'Customer email address'},
                {name: 'Appointment Time', type: 'datetime', required: true, desc: 'Date/time of appointment (YYYY-MM-DD HH:MM)'},
                {name: 'Reason', type: 'string', required: true, desc: 'Purpose of consultation / reason'},
                {name: 'Status', type: 'string', required: false, desc: 'Status (scheduled, completed, cancelled)'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="exportExcel('appointments')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('appointments')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('appointments.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>ADD APPOINTMENT</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Appointments</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Booked consultations</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #fbbf24; opacity: 0.15;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Pending / Scheduled</span>
            <h3 class="text-xl font-extrabold text-amber-400 mt-1 mono-text">{{ $stats['pending_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Upcoming appointments</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Completed</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">{{ $stats['completed_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Resolved visits</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #f43f5e; opacity: 0.15;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Cancelled</span>
            <h3 class="text-xl font-extrabold text-rose-500 mt-1 mono-text">{{ $stats['cancelled_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Cancelled bookings</span>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('appointments.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Ticket #, Customer or Phone..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
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
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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
                        <th class="py-3.5 px-6">Ticket No.</th>
                        <th class="py-3.5 px-6">Customer Details</th>
                        <th class="py-3.5 px-6">Scheduled Date & Time</th>
                        <th class="py-3.5 px-6">Reason</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Update Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($appointments as $apt)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-cyan-400 tracking-wider uppercase mono-text text-sm">
                                {{ $apt->appointment_no }}
                            </td>
                            <td class="py-3.5 px-6 font-bold text-slate-200">
                                {{ $apt->customer_name }}
                                <span class="text-[10px] text-slate-550 block font-mono mt-0.5">{{ $apt->customer_phone }}</span>
                                @if($apt->customer_email)
                                    <span class="text-[9px] text-slate-550 block font-sans">{{ $apt->customer_email }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-slate-350 mono-text text-xs">
                                {{ \Carbon\Carbon::parse($apt->appointment_time)->format('Y-m-d H:i') }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-300 max-w-xs truncate" title="{{ $apt->reason }}">
                                {{ $apt->reason }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                @if($apt->status === 'scheduled')
                                    <span class="px-2.5 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                        Scheduled
                                    </span>
                                @elseif($apt->status === 'completed')
                                    <span class="px-2.5 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Completed
                                    </span>
                                @elseif($apt->status === 'cancelled')
                                    <span class="px-2.5 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                @if($apt->status === 'scheduled')
                                    <div class="flex items-center justify-center gap-1.5">
                                        <form action="{{ route('appointments.status', $apt->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="px-2 py-1 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500 hover:text-slate-950 transition-colors border border-emerald-500/20 rounded font-bold uppercase text-[9px] tracking-wider">
                                                Complete
                                            </button>
                                        </form>
                                        <form action="{{ route('appointments.status', $apt->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="px-2 py-1 bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-slate-950 transition-colors border border-rose-500/20 rounded font-bold uppercase text-[9px] tracking-wider">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-slate-650 italic text-[10px] uppercase font-bold">Closed</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('appointments.edit', $apt->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all border border-slate-800" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('appointments.destroy', $apt->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this appointment?')" class="inline">
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
                                <i class="fa-solid fa-calendar-check text-2xl mb-2 block opacity-40"></i>
                                <span>No appointments scheduled.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($appointments->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $appointments->links() }}
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
