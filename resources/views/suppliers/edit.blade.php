@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('suppliers.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT SUPPLIER PROFILE</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Modify parameters for partner: {{ $supplier->company_name }}</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <!-- Company Name -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Company Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Representative Name -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Representative Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Phone -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Phone Number <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Email -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Tax Number -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Tax / VAT ID</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number', $supplier->tax_number) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Address -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Office / Warehouse Address</label>
                    <textarea name="address" rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">{{ old('address', $supplier->address) }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 border-t border-slate-800 pt-4">
                <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-slate-850 hover:bg-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
                <button type="submit" class="px-4 py-2 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan">
                    UPDATE PROFILE RECORD
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
