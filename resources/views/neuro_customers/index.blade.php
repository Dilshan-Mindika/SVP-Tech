@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">CUSTOMERS</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Manage customers, view loyalty points, and track purchases</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('customers', 'Customers', [
                {name: 'Name', type: 'string', required: true, desc: 'Customer full name'},
                {name: 'Phone', type: 'string', required: true, desc: 'Primary contact number (must be unique)'},
                {name: 'Email', type: 'string', required: false, desc: 'Customer email address'},
                {name: 'Address', type: 'string', required: false, desc: 'Home or office address'},
                {name: 'Loyalty Points', type: 'integer', required: false, desc: 'Initial loyalty points balance'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="exportExcel('customers')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('customers')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('neuro_customers.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-user-plus"></i>
                <span>ADD NEW CUSTOMER</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-users"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Customers</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Directory size</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">New This Month</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['new_this_month'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Registrations this month</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Active Members</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">{{ $stats['active_customers'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Purchased at least once</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #f43f5e; opacity: 0.15;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Outstanding Credit</span>
            <h3 class="text-xl font-extrabold text-rose-500 mt-1 mono-text">Rs. {{ number_format($stats['total_receivables'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Unpaid receivables due</span>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('neuro_customers.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, email..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
            </div>
            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition-colors">
                Search Directory
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                        <th class="py-3.5 px-6">Customer Name</th>
                        <th class="py-3.5 px-6">Phone</th>
                        <th class="py-3.5 px-6">Email</th>
                        <th class="py-3.5 px-6 text-right">Loyalty Points</th>
                        <th class="py-3.5 px-6 text-center">Member Since</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-850/20 transition-colors">
                            <!-- Name -->
                            <td class="py-3.5 px-6">
                                <div class="font-bold text-slate-200">{{ $customer->name }}</div>
                                <div class="text-[10px] text-slate-500 font-mono mt-0.5">ID: CUS-#{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <!-- Phone -->
                            <td class="py-3.5 px-6 text-slate-300 font-mono">{{ $customer->phone }}</td>
                            <!-- Email -->
                            <td class="py-3.5 px-6 text-slate-400">{{ $customer->email ?? '—' }}</td>
                            <!-- Loyalty Points -->
                            <td class="py-3.5 px-6 text-right">
                                <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold {{ $customer->loyalty_points > 0 ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-slate-800 text-slate-500 border border-slate-700' }}">
                                    {{ number_format($customer->loyalty_points) }} pts
                                </span>
                            </td>
                            <!-- Created At -->
                            <td class="py-3.5 px-6 text-center text-slate-400 font-mono">
                                {{ \Carbon\Carbon::parse($customer->created_at)->format('Y-m-d') }}
                            </td>
                            <!-- Actions -->
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('neuro_customers.show', $customer->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="View details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('neuro_customers.edit', $customer->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('neuro_customers.destroy'), $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?')" class="inline">
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
                            <td colspan="6" class="py-10 text-center text-slate-600">
                                <i class="fa-solid fa-users-slash text-2xl mb-2 block opacity-40"></i>
                                <span>No customers found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
