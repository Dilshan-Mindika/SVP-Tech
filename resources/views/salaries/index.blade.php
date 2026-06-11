@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">SALARIES</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Track monthly employee salary disbursements</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('salaries', 'Salaries', [
                {name: 'Payslip No', type: 'string', required: false, desc: 'Slip reference code (auto-generated if omitted)'},
                {name: 'Employee Email', type: 'string', required: true, desc: 'Work email address of registered Employee'},
                {name: 'Amount Paid', type: 'numeric', required: true, desc: 'Paid wages amount in Rs.'},
                {name: 'Paid For Month', type: 'string', required: true, desc: 'Disbursed month details (e.g. May 2026)'},
                {name: 'Payment Date', type: 'date', required: true, desc: 'Disbursement date (YYYY-MM-DD)'},
                {name: 'Payment Method', type: 'string', required: true, desc: 'Payment method channel (e.g. bank, cash)'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="exportExcel('salaries')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('salaries')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('salaries.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>ADD SALARY DISBURSEMENT</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Slips</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Disbursements logged</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Paid Out</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">Rs. {{ number_format($stats['total_paid'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Sum of paid salary amounts</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #f59e0b; opacity: 0.15;">
                <i class="fa-solid fa-spinner text-amber-500/20"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Pending Payments</span>
            <h3 class="text-xl font-extrabold text-amber-400 mt-1 mono-text">{{ $stats['pending_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Pending approval or payout</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #94a3b8; opacity: 0.15;">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Average Salary</span>
            <h3 class="text-xl font-extrabold text-slate-200 mt-1 mono-text">Rs. {{ number_format($stats['avg_salary'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Mean payout per slip</span>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('salaries.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Employee Name, Payslip #, Month..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
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
                        <th class="py-3.5 px-6">Payslip Number</th>
                        <th class="py-3.5 px-6">Employee</th>
                        <th class="py-3.5 px-6">Month</th>
                        <th class="py-3.5 px-6 text-center">Payment Date</th>
                        <th class="py-3.5 px-6">Method</th>
                        <th class="py-3.5 px-6 text-right">Amount</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($salaries as $sal)
                        <tr class="hover:bg-slate-850/20 transition-colors">
                            <!-- Payslip No -->
                            <td class="py-3.5 px-6 font-bold text-cyan-400 tracking-wider uppercase mono-text text-sm">
                                {{ $sal->payslip_no }}
                            </td>
                            <!-- Employee Name -->
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-200">
                                    {{ $sal->employee->name ?? 'N/A' }}
                                </div>
                                <div class="text-[10px] text-slate-550 font-mono mt-0.5">Role: {{ $sal->employee->designation ?? 'Unassigned' }}</div>
                            </td>
                            <!-- Payroll Month -->
                            <td class="py-3.5 px-6 font-semibold text-slate-300">
                                {{ $sal->paid_for_month }}
                            </td>
                            <!-- Payment Date -->
                            <td class="py-3.5 px-6 text-center text-slate-400 font-mono">
                                {{ \Carbon\Carbon::parse($sal->payment_date)->format('Y-m-d') }}
                            </td>
                            <!-- Payment Method -->
                            <td class="py-3.5 px-6">
                                <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-slate-800 text-slate-300 border border-slate-750">
                                    {{ $sal->payment_method }}
                                </span>
                            </td>
                            <!-- Disbursed Amount -->
                            <td class="py-3.5 px-6 text-right font-bold text-cyan-400 mono-text text-sm">
                                Rs. {{ number_format($sal->amount_paid, 2) }}
                            </td>
                            <!-- Status (All stored payouts are completed/posted) -->
                            <td class="py-3.5 px-6 text-center">
                                <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    Paid
                                </span>
                            </td>
                            <!-- Actions -->
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('salaries.edit', $sal->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all border border-slate-800" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('salaries.destroy', $sal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this salary disbursement?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-rose-500 transition-all border border-slate-800" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-wallet text-2xl mb-2 block opacity-40"></i>
                                <span>No salary disbursements found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($salaries->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $salaries->links() }}
            </div>
        @endif
    </div>

    <!-- Informational Note -->
    <div class="p-4 rounded-xl border border-cyan-500/10 bg-cyan-500/5 text-slate-400 text-xs leading-relaxed flex items-start gap-3">
        <i class="fa-solid fa-circle-info text-cyan-400 mt-0.5 shrink-0"></i>
        <div>
            <span class="font-bold text-slate-200">Note:</span>
            Recording a salary payout automatically adds it as a salary expense. This ensures your dashboard reports and profit calculations are accurate.
        </div>
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
