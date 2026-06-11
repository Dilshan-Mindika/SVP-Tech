@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT EXPENSE</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">Change expense details</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-lg transition-colors">
            BACK TO LIST
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('expenses.update', $expense->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Expense Number</label>
                <input type="text" value="{{ $expense->expense_no }}" class="w-full bg-slate-950 border border-slate-850 text-slate-400 rounded-lg px-3 py-2 text-xs focus:outline-none" disabled>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Expense Category <span class="text-rose-500">*</span></label>
                    <select name="category" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                        <option value="Rent" {{ $expense->category === 'Rent' ? 'selected' : '' }}>Rent</option>
                        <option value="Utilities" {{ $expense->category === 'Utilities' ? 'selected' : '' }}>Utilities (Water, Electricity, etc.)</option>
                        <option value="Salary" {{ $expense->category === 'Salary' ? 'selected' : '' }}>Salary Payouts</option>
                        <option value="Supplier Payment" {{ $expense->category === 'Supplier Payment' ? 'selected' : '' }}>Supplier Payments</option>
                        <option value="Office Supplies" {{ $expense->category === 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
                        <option value="Marketing" {{ $expense->category === 'Marketing' ? 'selected' : '' }}>Marketing & Ads</option>
                        <option value="Repairs" {{ $expense->category === 'Repairs' ? 'selected' : '' }}>Repairs & Maintenance</option>
                        <option value="Other" {{ $expense->category === 'Other' ? 'selected' : '' }}>Other Expenses</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Amount (Rs) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="amount" value="{{ $expense->amount }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Date Incurred <span class="text-rose-500">*</span></label>
                    <input type="date" name="date_incurred" value="{{ $expense->date_incurred }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Payment Method <span class="text-rose-500">*</span></label>
                    <select name="payment_method" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                        <option value="Cash" {{ $expense->payment_method === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Bank Transfer" {{ $expense->payment_method === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Cheque" {{ $expense->payment_method === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="Card" {{ $expense->payment_method === 'Card' ? 'selected' : '' }}>Card</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Details</label>
                <textarea name="details" rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">{{ $expense->details }}</textarea>
            </div>

            <div class="pt-4 flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400">
                    SAVE CHANGES
                </button>
                <a href="{{ route('expenses.index') }}" class="flex-1 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-center font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
