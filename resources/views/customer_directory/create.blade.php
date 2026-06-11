@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('customer_directory.index') }}" class="text-slate-400 hover:text-cyan-400 transition-colors p-2 hover:bg-slate-800/50 rounded-lg">
            <i class="fa-solid fa-chevron-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">ADD NEW CUSTOMER</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans">Register a new customer in the loyalty directory</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form method="POST" action="{{ route('customer_directory.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Full Name -->
                <div class="space-y-1.5">
                    <label for="name" class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block">Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        placeholder="e.g. Dilshan Perera"
                        class="w-full bg-slate-950 border border-slate-700 text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-500 transition-colors placeholder-slate-600">
                    @error('name')
                        <p class="text-rose-400 text-[10px] flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="space-y-1.5">
                    <label for="phone" class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block">Phone Number <span class="text-rose-500">*</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                        placeholder="e.g. +94771234567"
                        class="w-full bg-slate-950 border border-slate-700 text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-500 transition-colors placeholder-slate-600">
                    @error('phone')
                        <p class="text-rose-400 text-[10px] flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="space-y-1.5">
                    <label for="email" class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="e.g. customer@email.com"
                        class="w-full bg-slate-950 border border-slate-700 text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-500 transition-colors placeholder-slate-600">
                    @error('email')
                        <p class="text-rose-400 text-[10px] flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="space-y-1.5">
                    <label for="address" class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block">Address</label>
                    <textarea id="address" name="address" rows="3"
                        placeholder="Street, City, Province..."
                        class="w-full bg-slate-950 border border-slate-700 text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-cyan-500 transition-colors placeholder-slate-600 resize-none">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-rose-400 text-[10px] flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('customer_directory.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-lg text-sm transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold rounded-lg text-sm transition-colors shadow-neon-cyan hover:shadow-neon-cyan-lg">
                    <i class="fa-solid fa-user-plus mr-2"></i>Register Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
