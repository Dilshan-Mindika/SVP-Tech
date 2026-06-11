@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT RETURN</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">Change return record details</p>
        </div>
        <a href="{{ route('returns.index') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-lg transition-colors">
            BACK TO LIST
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('returns.update', $return->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Return Number</label>
                <input type="text" value="{{ $return->return_number }}" class="w-full bg-slate-950 border border-slate-850 text-slate-400 rounded-lg px-3 py-2 text-xs focus:outline-none" disabled>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Return Type <span class="text-rose-500">*</span></label>
                    <select name="type" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                        <option value="customer_return" {{ $return->type === 'customer_return' ? 'selected' : '' }}>Customer Return</option>
                        <option value="supplier_return" {{ $return->type === 'supplier_return' ? 'selected' : '' }}>Supplier Return</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Refund Amount (Rs) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="refund_amount" value="{{ $return->refund_amount }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Invoice Reference (For Customer Returns)</label>
                    <select name="invoice_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        <option value="">None</option>
                        @foreach($invoices as $i)
                            <option value="{{ $i->id }}" {{ $return->invoice_id == $i->id ? 'selected' : '' }}>{{ $i->invoice_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Supplier Link (For Supplier Returns)</label>
                    <select name="supplier_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        <option value="">None</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ $return->supplier_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Reason for Return <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>{{ $return->reason }}</textarea>
            </div>

            <div class="pt-4 flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400">
                    SAVE CHANGES
                </button>
                <a href="{{ route('returns.index') }}" class="flex-1 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-center font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
