@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT QUOTATION</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">Change quotation details</p>
        </div>
        <a href="{{ route('quotations.show', $quotation->id) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-lg transition-colors">
            BACK TO DETAILS
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('quotations.update', $quotation->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Quotation Number</label>
                <input type="text" value="{{ $quotation->quotation_number }}" class="w-full bg-slate-950 border border-slate-850 text-slate-400 rounded-lg px-3 py-2 text-xs focus:outline-none" disabled>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Link (Optional)</label>
                <select name="customer_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    <option value="">Guest (Manually Entered Below)</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ $quotation->customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Name</label>
                    <input type="text" name="customer_name" value="{{ $quotation->customer_name }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Phone</label>
                    <input type="text" name="customer_phone" value="{{ $quotation->customer_phone }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Valid Until <span class="text-rose-500">*</span></label>
                    <input type="date" name="valid_until" value="{{ \Carbon\Carbon::parse($quotation->valid_until)->format('Y-m-d') }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Status <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                        <option value="sent" {{ $quotation->status === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="accepted" {{ $quotation->status === 'accepted' ? 'selected' : '' }}>Accepted / Invoiced</option>
                        <option value="expired" {{ $quotation->status === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="cancelled" {{ $quotation->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Subtotal (Rs) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="subtotal" value="{{ $quotation->subtotal }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Tax (Rs) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="tax" value="{{ $quotation->tax }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Total (Rs) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="total" value="{{ $quotation->total }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Notes</label>
                <textarea name="notes" rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">{{ $quotation->notes }}</textarea>
            </div>

            <div class="pt-4 flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400">
                    SAVE CHANGES
                </button>
                <a href="{{ route('quotations.show', $quotation->id) }}" class="flex-1 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-center font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
