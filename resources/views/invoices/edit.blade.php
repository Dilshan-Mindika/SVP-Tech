@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT INVOICE</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">Change invoice details</p>
        </div>
        <a href="{{ route('invoices.show', $invoice->id) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-lg transition-colors">
            BACK TO DETAILS
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Invoice Number</label>
                <input type="text" value="{{ $invoice->invoice_number }}" class="w-full bg-slate-950 border border-slate-850 text-slate-400 rounded-lg px-3 py-2 text-xs focus:outline-none" disabled>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer</label>
                <select name="customer_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ $invoice->customer_id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Invoice Title</label>
                    <input type="text" name="title" value="{{ $invoice->title }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Sale Type</label>
                    <input type="text" name="sale_type" value="{{ $invoice->sale_type }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Assigned Employee</label>
                    <select name="employee_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        <option value="">None</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" {{ $invoice->employee_id == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Due Date</label>
                    <input type="date" name="due_date" value="{{ $invoice->due_date }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Payment Method</label>
                    <select name="payment_method" id="paymentMethodSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                        <option value="Cash" {{ $invoice->payment_method === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Card" {{ $invoice->payment_method === 'Card' ? 'selected' : '' }}>Card</option>
                        <option value="Bank Transfer" {{ ($invoice->payment_method === 'Bank Transfer' || $invoice->payment_method === 'Bank') ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Cheque" {{ $invoice->payment_method === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="Koko" {{ $invoice->payment_method === 'Koko' ? 'selected' : '' }}>Koko</option>
                        <option value="Payzy" {{ $invoice->payment_method === 'Payzy' ? 'selected' : '' }}>Payzy</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Is Paid</label>
                    <select name="is_paid" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                        <option value="1" {{ $invoice->is_paid ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !$invoice->is_paid ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>

            <div id="bankAccountContainer" style="display: {{ ($invoice->payment_method === 'Bank Transfer' || $invoice->payment_method === 'Bank') ? 'block' : 'none' }};">
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Select Bank Account *</label>
                <select name="bank_account_id" id="bankAccountSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    <option value="">Select Account...</option>
                    @foreach($bankAccounts as $ba)
                        <option value="{{ $ba->id }}" {{ $invoice->bank_account_id == $ba->id ? 'selected' : '' }}>{{ $ba->bank_name }} - {{ substr($ba->account_number, -4) }} ({{ $ba->account_name }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-4 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Subtotal (Rs)</label>
                    <input type="number" step="0.01" name="subtotal" value="{{ $invoice->subtotal }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Tax (Rs)</label>
                    <input type="number" step="0.01" name="tax" value="{{ $invoice->tax }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Discount (Rs)</label>
                    <input type="number" step="0.01" name="discount" value="{{ $invoice->discount }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Total (Rs)</label>
                    <input type="number" step="0.01" name="total" value="{{ $invoice->total }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Paid (Rs)</label>
                <input type="number" step="0.01" name="customer_paid" value="{{ $invoice->customer_paid }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Special Note</label>
                <textarea name="special_note" rows="2" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">{{ $invoice->special_note }}</textarea>
            </div>

            <div class="pt-4 flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400">
                    SAVE CHANGES
                </button>
                <a href="{{ route('invoices.show', $invoice->id) }}" class="flex-1 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-center font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('paymentMethodSelect');
    const bankAccountContainer = document.getElementById('bankAccountContainer');
    const bankAccountSelect = document.getElementById('bankAccountSelect');

    paymentMethodSelect.addEventListener('change', function() {
        if (this.value === 'Bank Transfer') {
            bankAccountContainer.style.display = 'block';
            bankAccountSelect.required = true;
        } else {
            bankAccountContainer.style.display = 'none';
            bankAccountSelect.required = false;
            bankAccountSelect.value = '';
        }
    });
});
</script>
@endsection
