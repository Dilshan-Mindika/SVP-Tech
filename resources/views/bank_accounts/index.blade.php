@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-4 border-b border-slate-800 gap-4">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">BANK ACCOUNTS</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Manage corporate bank transfer payment destinations</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            @if(Auth::user()->hasPermission('create-bank-accounts'))
                <!-- Import Excel/CSV -->
                <button onclick="showImportModal('bank_accounts', 'Bank Accounts', [
                    {name: 'Bank Name', type: 'string', required: true, desc: 'Name of the banking institute'},
                    {name: 'Account Name', type: 'string', required: true, desc: 'Account holder name'},
                    {name: 'Account Number', type: 'string', required: true, desc: 'Unique bank account number (must be unique)'},
                    {name: 'Branch', type: 'string', required: true, desc: 'Branch name'},
                    {name: 'Is Active', type: 'boolean', required: false, desc: 'Account active status (1 for Active, 0 for Inactive)'}
                ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-file-import text-cyan-400"></i>
                    <span>IMPORT</span>
                </button>
            @endif
            
            <!-- Export Excel -->
            <button onclick="exportExcel('bank_accounts')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('bank_accounts')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            @if(Auth::user()->hasPermission('create-bank-accounts'))
                <a href="{{ route('bank_accounts.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan flex items-center gap-1.5 uppercase tracking-wider">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>ADD ACCOUNT</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Stat Card 1 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Accounts</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text font-black">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Configured bank destinations</span>
        </div>
        
        <!-- Stat Card 2 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Active Accounts</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text font-black">{{ $stats['active_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Currently open for transfers</span>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #fb7185; opacity: 0.15;">
                <i class="fa-solid fa-ban"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Inactive Accounts</span>
            <h3 class="text-xl font-extrabold text-rose-400 mt-1 mono-text font-black">{{ $stats['inactive_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Deactivated/Closed channels</span>
        </div>

        <!-- Stat Card 4 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #fbbf24; opacity: 0.15;">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Paid Volume</span>
            <h3 class="text-xl font-extrabold text-amber-400 mt-1 mono-text font-black">Rs. {{ number_format($stats['bank_transactions_value'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Overall bank transfer revenue</span>
        </div>
    </div>

    <!-- Search/Filters -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 flex flex-col md:flex-row gap-4 items-center justify-between">
        <form action="{{ route('bank_accounts.index') }}" method="GET" class="w-full md:w-96 flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by bank name, account, number..." class="flex-grow bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
            <button type="submit" class="px-4 py-1.5 bg-slate-850 hover:bg-slate-800 text-slate-200 font-bold rounded-lg text-xs transition-colors">
                SEARCH
            </button>
            @if(request('search'))
                <a href="{{ route('bank_accounts.index') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-750 text-slate-400 rounded-lg text-xs flex items-center justify-center">
                    CLEAR
                </a>
            @endif
        </form>
    </div>


    <!-- Data Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                        <th class="py-3 px-6">Bank Name</th>
                        <th class="py-3 px-6">Account Name</th>
                        <th class="py-3 px-6">Account Number</th>
                        <th class="py-3 px-6">Branch</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($bankAccounts as $acc)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-slate-200">{{ $acc->bank_name }}</td>
                            <td class="py-3.5 px-6 font-semibold text-slate-300">{{ $acc->account_name }}</td>
                            <td class="py-3.5 px-6 text-cyan-400 font-bold font-mono">{{ $acc->account_number }}</td>
                            <td class="py-3.5 px-6 text-slate-400">{{ $acc->branch ?? 'N/A' }}</td>
                            <td class="py-3.5 px-6 text-center">
                                @if($acc->is_active)
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        ACTIVE
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-slate-800 text-slate-400 border border-slate-700">
                                        INACTIVE
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(Auth::user()->hasPermission('update-bank-accounts'))
                                        <a href="{{ route('bank_accounts.edit', $acc->id) }}" class="p-1 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded border border-slate-800 transition-colors" title="Edit">
                                            <i class="fa-solid fa-pen-to-square w-4 text-center"></i>
                                        </a>
                                    @endif

                                    @if(Auth::user()->hasPermission('delete-bank-accounts'))
                                        <form action="{{ route('bank_accounts.destroy', $acc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this bank account?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 bg-rose-950/20 border border-rose-900/50 hover:bg-rose-900/20 text-rose-400 rounded transition-colors" title="Delete">
                                                <i class="fa-solid fa-trash w-4 text-center"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500 italic">
                                No bank accounts registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bankAccounts->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-950/20">
                {{ $bankAccounts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
