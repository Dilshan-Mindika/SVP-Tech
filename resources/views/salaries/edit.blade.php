@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT SALARY RECORD</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">Change salary payout details</p>
        </div>
        <a href="{{ route('salaries.index') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-lg transition-colors">
            BACK TO LIST
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('salaries.update', $salary->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Payslip Number</label>
                <input type="text" value="{{ $salary->payslip_no }}" class="w-full bg-slate-950 border border-slate-850 text-slate-400 rounded-lg px-3 py-2 text-xs focus:outline-none" disabled>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Employee <span class="text-rose-500">*</span></label>
                <select name="employee_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                    @foreach($employees as $e)
                        <option value="{{ $e->id }}" {{ $salary->employee_id == $e->id ? 'selected' : '' }}>{{ $e->name }} ({{ $e->designation }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Amount Paid (Rs) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="amount_paid" value="{{ $salary->amount_paid }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Paid For Month <span class="text-rose-500">*</span></label>
                    <input type="text" name="paid_for_month" value="{{ $salary->paid_for_month }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required placeholder="e.g. May 2026">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Payment Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="payment_date" value="{{ $salary->payment_date }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Payment Method <span class="text-rose-500">*</span></label>
                    <select name="payment_method" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                        <option value="Cash" {{ $salary->payment_method === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Bank Transfer" {{ $salary->payment_method === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Cheque" {{ $salary->payment_method === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400">
                    SAVE CHANGES
                </button>
                <a href="{{ route('salaries.index') }}" class="flex-1 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-center font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
