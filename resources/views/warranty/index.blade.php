@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">WARRANTY CLAIMS</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Track customer warranties and supplier replacements</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <!-- Import Excel/CSV -->
            <button onclick="showImportModal('warranties', 'Warranty Claims', [
                {name: 'Claim Number', type: 'string', required: true, desc: 'Unique Claim Ticket ID'},
                {name: 'Customer Phone', type: 'string', required: true, desc: 'Registered Customer Phone'},
                {name: 'Invoice Number', type: 'string', required: true, desc: 'Purchased Invoice number'},
                {name: 'Product SKU', type: 'string', required: true, desc: 'Claimed Product SKU'},
                {name: 'Serial Number', type: 'string', required: true, desc: 'Item physical serial number'},
                {name: 'Claim Date', type: 'date', required: true, desc: 'Claimed Date (YYYY-MM-DD)'},
                {name: 'Issue Description', type: 'string', required: true, desc: 'Observed hardware issue details'},
                {name: 'Status', type: 'string', required: false, desc: 'Status (pending, approved, rejected)'}
            ])" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-import text-cyan-400"></i>
                <span>IMPORT</span>
            </button>
            
            <!-- Export Excel -->
            <button onclick="exportExcel('warranties')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-emerald-400"></i>
                <span>EXPORT EXCEL</span>
            </button>

            <!-- Export PDF -->
            <button onclick="exportPDF('warranties')" class="px-3.5 py-2 bg-slate-900 border border-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-all hover:bg-slate-800 hover:border-slate-700 flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                <span>EXPORT PDF</span>
            </button>

            <a href="{{ route('warranty.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>NEW WARRANTY CLAIM</span>
            </a>
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Claims</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Logged claims</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #fbbf24; opacity: 0.15;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Pending Intake</span>
            <h3 class="text-xl font-extrabold text-amber-400 mt-1 mono-text">{{ $stats['pending_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Claims awaiting action</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Approved/Replaced</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">{{ $stats['approved_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Settled claims</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #f43f5e; opacity: 0.15;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Rejected</span>
            <h3 class="text-xl font-extrabold text-rose-500 mt-1 mono-text">{{ $stats['rejected_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Claims turned down</span>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('warranty.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex-grow w-full md:max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Claim #, Serial # or Customer..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
            </div>

            <div class="flex flex-wrap gap-3 w-full md:w-auto">
                <!-- Status Filter -->
                <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-slate-300 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    <option value="all">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="sent_to_supplier" {{ request('status') === 'sent_to_supplier' ? 'selected' : '' }}>Sent to Supplier</option>
                    <option value="replaced" {{ request('status') === 'replaced' ? 'selected' : '' }}>Replaced</option>
                    <option value="returned_to_customer" {{ request('status') === 'returned_to_customer' ? 'selected' : '' }}>Returned to Customer</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected Claim</option>
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
                        <th class="py-3.5 px-6">Claim ID</th>
                        <th class="py-3.5 px-6">Customer Details</th>
                        <th class="py-3.5 px-6">Product & Serial</th>
                        <th class="py-3.5 px-6">Date Logged</th>
                        <th class="py-3.5 px-6 text-center">Linked Invoice</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($claims as $c)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-cyan-400 tracking-wider uppercase mono-text text-sm">
                                {{ $c->claim_number }}
                            </td>
                            <td class="py-3.5 px-6 font-bold text-slate-200">
                                {{ $c->customer->name }}
                                <span class="text-[10px] text-slate-500 block font-mono mt-0.5">{{ $c->customer->phone }}</span>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="text-slate-300 font-semibold block">{{ $c->product->name }}</span>
                                <span class="text-[10px] text-cyan-400/80 block font-mono mt-0.5">S/N: {{ $c->serial_number ?: 'N/A' }}</span>
                            </td>
                            <td class="py-3.5 px-6 text-slate-400 mono-text">
                                {{ \Carbon\Carbon::parse($c->claim_date)->format('Y-m-d') }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                @if($c->invoice)
                                    <a href="{{ route('invoices.show', $c->invoice->id) }}" class="text-cyan-400 font-bold hover:underline font-mono text-[11px]">
                                        {{ $c->invoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="text-slate-600 text-[10px]">Over-the-counter</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                @if(Auth::user()->hasPermission('update-warranty'))
                                    <form action="{{ route('warranty.status', $c->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-xs rounded px-2 py-1 focus:outline-none focus:border-cyan-500 transition-colors uppercase font-bold text-[10px] tracking-wider cursor-pointer 
                                            @if($c->status === 'pending') text-amber-400 border-amber-500/30
                                            @elseif($c->status === 'in_review') text-indigo-400 border-indigo-500/30
                                            @elseif($c->status === 'sent_to_supplier') text-violet-400 border-violet-500/30
                                            @elseif($c->status === 'replaced') text-emerald-400 border-emerald-500/30
                                            @elseif($c->status === 'returned_to_customer') text-blue-400 border-blue-500/30
                                            @elseif($c->status === 'rejected') text-rose-400 border-rose-500/30
                                            @endif">
                                            <option value="pending" {{ $c->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in_review" {{ $c->status === 'in_review' ? 'selected' : '' }}>In Review</option>
                                            <option value="sent_to_supplier" {{ $c->status === 'sent_to_supplier' ? 'selected' : '' }}>Sent to Supplier</option>
                                            <option value="replaced" {{ $c->status === 'replaced' ? 'selected' : '' }}>Replaced</option>
                                            <option value="returned_to_customer" {{ $c->status === 'returned_to_customer' ? 'selected' : '' }}>Returned to Customer</option>
                                            <option value="rejected" {{ $c->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </form>
                                @else
                                    @if($c->status === 'pending')
                                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            Pending
                                        </span>
                                    @elseif($c->status === 'in_review')
                                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                            In Review
                                        </span>
                                    @elseif($c->status === 'sent_to_supplier')
                                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-violet-500/10 text-violet-400 border border-violet-500/20">
                                            Sent to Supplier
                                        </span>
                                    @elseif($c->status === 'replaced')
                                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Replaced
                                        </span>
                                    @elseif($c->status === 'returned_to_customer')
                                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                            Returned
                                        </span>
                                    @elseif($c->status === 'rejected')
                                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            Rejected
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('warranty.show', $c->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="View details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('warranty.edit', $c->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('warranty.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this warranty claim?')" class="inline">
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
                                <i class="fa-solid fa-shield-halved text-2xl mb-2 block opacity-40"></i>
                                <span>No warranty claims found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($claims->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $claims->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
