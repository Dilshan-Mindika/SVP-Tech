@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">SUPPLIERS LIST</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">All suppliers</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('suppliers', 'Suppliers', [
                {name: 'Contact Person', type: 'string', required: true, desc: 'Primary contact name'},
                {name: 'Company Name', type: 'string', required: true, desc: 'Registered business/supplier entity name'},
                {name: 'Phone', type: 'string', required: true, desc: 'Supplier phone number (must be unique)'},
                {name: 'Email', type: 'string', required: false, desc: 'Supplier email address'},
                {name: 'Address', type: 'string', required: false, desc: 'Registered office address'},
                {name: 'Tax Number', type: 'string', required: false, desc: 'VAT/TIN registered number'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="exportExcel('suppliers')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('suppliers')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('suppliers.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>CREATE SUPPLIER</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-handshake"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Suppliers</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Partners registered</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-truck-moving"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total GRN Deliveries</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_deliveries'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Shipments received</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Supplied Value</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">Rs. {{ number_format($stats['supplied_value'], 2) }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Sum of purchase totals</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Active (This Month)</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">{{ $stats['active_suppliers'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Supplied goods this month</span>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('suppliers.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Company, Rep Name or Phone..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
            </div>
            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition-colors">
                Search
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                        <th class="py-3.5 px-6">Company Name</th>
                        <th class="py-3.5 px-6">Contact Name</th>
                        <th class="py-3.5 px-6">Phone</th>
                        <th class="py-3.5 px-6">Email</th>
                        <th class="py-3.5 px-6 font-mono">Tax ID</th>
                        <th class="py-3.5 px-6">Address</th>
                        <th class="py-3.5 px-6 text-center">Options</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-slate-200 text-sm">
                                {{ $supplier->company_name }}
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-slate-300">
                                {{ $supplier->name }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-400 mono-text font-bold">
                                {{ $supplier->phone }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-300">
                                {{ $supplier->email ?: 'N/A' }}
                            </td>
                            <td class="py-3.5 px-6 text-cyan-400 uppercase font-bold tracking-wider mono-text">
                                {{ $supplier->tax_number ?: 'N/A' }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-500 max-w-xs truncate">
                                {{ $supplier->address ?: 'N/A' }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 hover:text-cyan-400 rounded transition-all" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this supplier?')" class="inline">
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
                            <td colspan="7" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-handshake text-2xl mb-2 block opacity-40"></i>
                                <span>No suppliers registered.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($suppliers->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
