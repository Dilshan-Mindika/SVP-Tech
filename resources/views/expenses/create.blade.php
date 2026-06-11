@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('expenses.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">LOG BUSINESS EXPENSE</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Enter overheads, repairs parts expenses, and general utility invoices</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 relative overflow-hidden">
        <!-- Accent light -->
        <div class="absolute -top-24 -right-24 h-48 w-48 rounded-full bg-cyan-500/5 blur-3xl"></div>

        <form action="{{ route('expenses.store') }}" method="POST" class="space-y-6">
            @csrf

            <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">Expense Transaction Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Expense Category <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-semibold">
                        <option value="">Select Category...</option>
                        <option value="Rent" {{ old('category') === 'Rent' ? 'selected' : '' }}>Rent</option>
                        <option value="Utilities" {{ old('category') === 'Utilities' ? 'selected' : '' }}>Utilities (Power/Water/Net)</option>
                        <option value="Salary" {{ old('category') === 'Salary' ? 'selected' : '' }}>Salary / Commissions</option>
                        <option value="Repair Parts" {{ old('category') === 'Repair Parts' ? 'selected' : '' }}>Repair Parts / Materials</option>
                        <option value="Marketing" {{ old('category') === 'Marketing' ? 'selected' : '' }}>Marketing & Advertisements</option>
                        <option value="Other" {{ old('category') === 'Other' ? 'selected' : '' }}>Other Miscellaneous</option>
                    </select>
                </div>

                <!-- Cost Amount -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Transaction Cost Amount (Rs.) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount" required step="0.01" min="0.01" placeholder="0.00" value="{{ old('amount') }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-mono font-bold">
                </div>

                <!-- Date Incurred -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Date Incurred <span class="text-rose-500">*</span></label>
                    <input type="date" name="date_incurred" required value="{{ old('date_incurred', date('Y-m-d')) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Payment Channel <span class="text-rose-500">*</span></label>
                    <select name="payment_method" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Card" {{ old('payment_method') === 'Card' ? 'selected' : '' }}>Credit / Debit Card</option>
                        <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Check" {{ old('payment_method') === 'Check' ? 'selected' : '' }}>Check</option>
                    </select>
                </div>

                <!-- Description / Details -->
                <div class="md:col-span-2">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Additional Transaction Notes & Details</label>
                    <textarea name="details" placeholder="Describe the transaction detail, e.g. 'April office power bill invoice payment receipt #20138' or 'Procured spare screens for repairs queue'..." rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">{{ old('details') }}</textarea>
                </div>
            </div>

            <!-- Form Action -->
            <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
                <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-750 text-slate-350 hover:text-slate-200 text-xs font-bold rounded-lg transition-colors font-sans">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-black rounded-lg uppercase tracking-wider transition-all shadow-neon-cyan font-sans">
                    COMMIT LOG
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
