@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <a href="{{ route('warranty.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">{{ $claim->claim_number }}</h1>
                    
                    @if($claim->status === 'pending')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            Pending Intake
                        </span>
                    @elseif($claim->status === 'sent_to_supplier')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-violet-500/10 text-violet-400 border border-violet-500/20">
                            RMA (Supplier)
                        </span>
                    @elseif($claim->status === 'replaced')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            Replaced
                        </span>
                    @elseif($claim->status === 'returned_to_customer')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
                            Returned to Customer
                        </span>
                    @elseif($claim->status === 'rejected')
                        <span class="px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                            Rejected
                        </span>
                    @endif
                </div>
                <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Warranty Claim Details</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('warranty.edit', $claim->id) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-lg text-xs transition-colors flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>EDIT</span>
            </a>
            <form action="{{ route('warranty.destroy', $claim->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this warranty claim?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-rose-900/20 hover:bg-rose-900/40 text-rose-400 font-bold rounded-lg text-xs transition-colors flex items-center gap-2 border border-rose-800/40">
                    <i class="fa-solid fa-trash"></i>
                    <span>DELETE</span>
                </button>
            </form>
            <button onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-slate-200 border border-slate-800 rounded-lg text-xs font-bold transition-all">
                <i class="fa-solid fa-print"></i>
                <span>PRINT</span>
            </button>
        </div>
    </div>

    <!-- Info Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main details -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 md:col-span-2 space-y-4">
            <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">Claim Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Customer</span>
                    <span class="text-slate-200 font-bold text-sm">{{ $claim->customer->name }}</span>
                    <span class="text-[10px] text-slate-450 block font-mono">{{ $claim->customer->phone }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Product</span>
                    <span class="text-slate-200 font-semibold">{{ $claim->product->name }}</span>
                    <span class="text-[10px] text-cyan-400 block font-mono">SKU: {{ $claim->product->sku }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Date Received</span>
                    <span class="text-slate-200 font-mono">{{ \Carbon\Carbon::parse($claim->claim_date)->format('Y-m-d') }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Serial Number</span>
                    <span class="text-cyan-400 font-bold font-mono uppercase text-sm">{{ $claim->serial_number ?: 'Not Recorded' }}</span>
                </div>
                @if($claim->invoice)
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Associated Invoice</span>
                        <a href="{{ route('invoices.show', $claim->invoice->id) }}" class="text-cyan-400 font-bold hover:underline font-mono">
                            {{ $claim->invoice->invoice_number }}
                        </a>
                    </div>
                @endif
                @if($claim->closed_date)
                    <div>
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Closure Date</span>
                        <span class="text-slate-200 font-mono">{{ $claim->closed_date }}</span>
                    </div>
                @endif
                <div class="md:col-span-2">
                    <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Fault Description</span>
                    <div class="p-3 bg-slate-950 border border-slate-800 rounded-lg text-slate-350 mt-1">
                        {{ $claim->issue_description }}
                    </div>
                </div>
                @if($claim->action_taken)
                    <div class="md:col-span-2">
                        <span class="text-slate-500 block uppercase tracking-wider text-[9px] font-bold">Action Taken</span>
                        <div class="p-3 bg-cyan-950/20 border border-cyan-500/20 rounded-lg text-cyan-400 mt-1">
                            {{ $claim->action_taken }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Operations Console Form -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
            <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800/80 pb-2">Update Warranty Status</h2>
            
            <form action="{{ route('warranty.status', $claim->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Status</label>
                    <select name="status" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        <option value="pending" {{ $claim->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="sent_to_supplier" {{ $claim->status === 'sent_to_supplier' ? 'selected' : '' }}>Sent to Supplier</option>
                        <option value="replaced" {{ $claim->status === 'replaced' ? 'selected' : '' }}>Replaced</option>
                        <option value="returned_to_customer" {{ $claim->status === 'returned_to_customer' ? 'selected' : '' }}>Returned to Customer</option>
                        <option value="rejected" {{ $claim->status === 'rejected' ? 'selected' : '' }}>Rejected Claim</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Action Note</label>
                    <textarea name="action_taken" placeholder="Enter replacement serial details, vendor RMA numbers or rejection reasons..." rows="5" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">{{ $claim->action_taken }}</textarea>
                </div>
                <button type="submit" class="w-full py-2 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan">
                    UPDATE STATUS
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
