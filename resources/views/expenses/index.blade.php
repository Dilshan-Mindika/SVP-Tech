@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">EXPENSES</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Track business expenses, utility bills, and purchasing costs</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('expenses', 'Expenses', [
                {name: 'Expense No', type: 'string', required: false, desc: 'Expense slip code (auto-generated if omitted)'},
                {name: 'Category', type: 'string', required: true, desc: 'Expense category (Utility, Rent, salaries, Marketing, etc.)'},
                {name: 'Amount Spent', type: 'numeric', required: true, desc: 'Expense amount in Rs.'},
                {name: 'Date Incurred', type: 'date', required: true, desc: 'Transaction date (YYYY-MM-DD)'},
                {name: 'Payment Method', type: 'string', required: true, desc: 'Payment channel (cash or bank)'},
                {name: 'Details', type: 'string', required: false, desc: 'Detailed notes about the expense'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="exportExcel('expenses')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('expenses')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('expenses.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-receipt"></i>
                <span>ADD EXPENSE</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Stat Card 1 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Expenses</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text font-black">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Logged expenses</span>
        </div>
        
        <!-- Stat Card 2 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #fb7185; opacity: 0.15;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Spent</span>
            <h3 class="text-xl font-extrabold text-rose-400 mt-1 mono-text font-black">Rs. {{ number_format($stats['total_amount'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Sum of all expenses</span>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Cash Payments</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text font-black">Rs. {{ number_format($stats['cash_amount'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Paid in cash</span>
        </div>

        <!-- Stat Card 4 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #fbbf24; opacity: 0.15;">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Bank Transfers</span>
            <h3 class="text-xl font-extrabold text-amber-400 mt-1 mono-text font-black">Rs. {{ number_format($stats['bank_amount'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Paid via bank transfer</span>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('expenses.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Expense #, Category or Details..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
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
                    <span class="text-slate-550 text-xs">to</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                </div>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition-colors font-sans">
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
                        <th class="py-3.5 px-6">Expense Number</th>
                        <th class="py-3.5 px-6">Category</th>
                        <th class="py-3.5 px-6">Description</th>
                        <th class="py-3.5 px-6 text-center">Date</th>
                        <th class="py-3.5 px-6">Method</th>
                        <th class="py-3.5 px-6 text-right">Amount</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($expenses as $exp)
                        <tr class="hover:bg-slate-850/20 transition-colors">
                            <!-- Expense No -->
                            <td class="py-3.5 px-6 font-bold text-cyan-400 tracking-wider uppercase mono-text text-sm">
                                {{ $exp->expense_no }}
                            </td>
                            <!-- Category -->
                            <td class="py-3.5 px-6">
                                @php
                                    $cat = strtolower($exp->category);
                                    $bgClass = 'bg-slate-800 text-slate-300 border-slate-750';
                                    if ($cat === 'rent') $bgClass = 'bg-rose-500/10 text-rose-400 border border-rose-500/20';
                                    elseif ($cat === 'utilities') $bgClass = 'bg-amber-500/10 text-amber-400 border border-amber-500/20';
                                    elseif ($cat === 'salary') $bgClass = 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20';
                                    elseif ($cat === 'repair parts') $bgClass = 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20';
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider {{ $bgClass }}">
                                    {{ $exp->category }}
                                </span>
                            </td>
                            <!-- Details -->
                            <td class="py-3.5 px-6 text-slate-300 font-medium max-w-xs truncate">
                                {{ $exp->details ?: 'No detailed logs.' }}
                            </td>
                            <!-- Date Incurred -->
                            <td class="py-3.5 px-6 text-center text-slate-400">
                                {{ \Carbon\Carbon::parse($exp->date_incurred)->format('Y-m-d') }}
                            </td>
                            <!-- Payment Method -->
                            <td class="py-3.5 px-6 text-slate-300 font-medium">
                                {{ $exp->payment_method }}
                            </td>
                            <!-- Amount -->
                            <td class="py-3.5 px-6 text-right text-rose-400 font-black mono-text text-sm">
                                Rs. {{ number_format($exp->amount, 2) }}
                            </td>
                            <!-- Options -->
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex justify-center items-center gap-1.5">
                                    <a href="{{ route('expenses.edit', $exp->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 hover:text-cyan-400 rounded transition-all border border-slate-800" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expense?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 hover:text-rose-500 rounded transition-all border border-slate-800" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-receipt text-2xl mb-2 block opacity-40"></i>
                                <span>No expenses found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($expenses->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $expenses->links() }}
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
