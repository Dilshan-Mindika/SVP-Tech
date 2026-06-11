@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('salaries.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">RECORD SALARY DISBURSEMENT</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Disburse wages and generate general ledger payroll accounts records</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 relative overflow-hidden">
        <!-- Accent light -->
        <div class="absolute -top-24 -right-24 h-48 w-48 rounded-full bg-cyan-500/5 blur-3xl"></div>

        <form action="{{ route('salaries.store') }}" method="POST" id="salaryForm" class="space-y-6">
            @csrf

            <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">Wage Disbursement Input</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee -->
                <div class="md:col-span-2">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Select Employee Profile <span class="text-rose-500">*</span></label>
                    <select name="employee_id" id="employeeSelect" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-semibold">
                        <option value="">Choose active staff member...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" data-salary="{{ $emp->salary_amount }}">{{ $emp->name }} [Role: {{ $emp->designation }}] (Base: Rs. {{ number_format($emp->salary_amount, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Disbursed Amount -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Disbursed Wages Amount (Rs.) <span class="text-rose-500">*</span></label>
                    <input type="number" name="amount_paid" id="amountInput" required step="0.01" min="0" placeholder="0.00" value="{{ old('amount_paid') }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-mono font-bold">
                    <p class="text-[10px] text-slate-500 mt-1">Defaults to the employee's base salary but can be overridden for bonuses or deductions.</p>
                </div>

                <!-- Paid for Month -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Payroll Period Month <span class="text-rose-500">*</span></label>
                    <select name="paid_for_month" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        @php
                            $currentDate = now();
                            $months = [];
                            for ($i = -3; $i <= 1; $i++) {
                                $months[] = $currentDate->copy()->addMonths($i)->format('F Y');
                            }
                        @endphp
                        @foreach($months as $month)
                            <option value="{{ $month }}" {{ old('paid_for_month', $currentDate->format('F Y')) === $month ? 'selected' : '' }}>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Date -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Disbursement Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="payment_date" required value="{{ old('payment_date', date('Y-m-d')) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Disbursement Method <span class="text-rose-500">*</span></label>
                    <select name="payment_method" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        <option value="Bank Transfer" {{ old('payment_method') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Check" {{ old('payment_method') === 'Check' ? 'selected' : '' }}>Check</option>
                    </select>
                </div>
            </div>

            <!-- Form Action -->
            <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
                <a href="{{ route('salaries.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-750 text-slate-350 hover:text-slate-200 text-xs font-bold rounded-lg transition-colors font-sans">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-black rounded-lg uppercase tracking-wider transition-all shadow-neon-cyan font-sans">
                    POST DISBURSEMENT
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeSelect = document.getElementById('employeeSelect');
        const amountInput = document.getElementById('amountInput');

        employeeSelect.addEventListener('change', function() {
            const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
            const baseSalary = selectedOption.getAttribute('data-salary');
            if (baseSalary) {
                amountInput.value = parseFloat(baseSalary).toFixed(2);
            } else {
                amountInput.value = '';
            }
        });
    });
</script>
@endsection
