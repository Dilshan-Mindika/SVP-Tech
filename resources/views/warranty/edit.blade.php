@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT WARRANTY CLAIM</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">Change warranty claim details</p>
        </div>
        <a href="{{ route('warranty.show', $claim->id) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-lg transition-colors">
            BACK TO DETAILS
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('warranty.update', $claim->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Claim Number</label>
                <input type="text" value="{{ $claim->claim_number }}" class="w-full bg-slate-950 border border-slate-850 text-slate-400 rounded-lg px-3 py-2 text-xs focus:outline-none" disabled>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer <span class="text-rose-500">*</span></label>
                <select name="customer_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ $claim->customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Product <span class="text-rose-500">*</span></label>
                    <select name="product_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ $claim->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Serial Number</label>
                    <input type="text" name="serial_number" value="{{ $claim->serial_number }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Invoice <span class="text-rose-500">*</span></label>
                    <select name="invoice_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                        @foreach($invoices as $i)
                            <option value="{{ $i->id }}" {{ $claim->invoice_id == $i->id ? 'selected' : '' }}>{{ $i->invoice_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Claim Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="claim_date" value="{{ $claim->claim_date }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Status <span class="text-rose-500">*</span></label>
                <select name="status" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                    <option value="pending" {{ $claim->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_review" {{ $claim->status === 'in_review' ? 'selected' : '' }}>In Review</option>
                    <option value="sent_to_supplier" {{ $claim->status === 'sent_to_supplier' ? 'selected' : '' }}>Sent to Supplier</option>
                    <option value="replaced" {{ $claim->status === 'replaced' ? 'selected' : '' }}>Replaced</option>
                    <option value="returned_to_customer" {{ $claim->status === 'returned_to_customer' ? 'selected' : '' }}>Returned to Customer</option>
                    <option value="rejected" {{ $claim->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Issue Description <span class="text-rose-500">*</span></label>
                <textarea name="issue_description" rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>{{ $claim->issue_description }}</textarea>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Action Taken</label>
                <textarea name="action_taken" rows="2" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">{{ $claim->action_taken }}</textarea>
            </div>

            <div class="pt-4 flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400">
                    SAVE CHANGES
                </button>
                <a href="{{ route('warranty.show', $claim->id) }}" class="flex-1 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-center font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
