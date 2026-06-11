@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('appointments.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">BOOK SERVICE APPOINTMENT</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Schedule consultations, diagnostic drop-offs, or custom rig build discussions</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('appointments.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <!-- Customer Name -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_name" required placeholder="e.g. Amanda Perera" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Customer Phone -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Contact Phone <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_phone" required placeholder="e.g. +94 77 555 4444" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-mono">
                </div>

                <!-- Customer Email -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Email Address</label>
                    <input type="email" name="customer_email" placeholder="e.g. customer@example.com" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Appointment Time -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Appointment Date & Time <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="appointment_time" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-mono text-slate-300">
                </div>

                <!-- Reason -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Consultation Reason / Service Required <span class="text-rose-500">*</span></label>
                    <textarea name="reason" required placeholder="Describe the consultation purpose or diagnosed issue (e.g. Custom Hardline water-cooling loop design and pricing)..." rows="4" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors"></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 border-t border-slate-800 pt-4">
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 bg-slate-850 hover:bg-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
                <button type="submit" class="px-4 py-2 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan">
                    SCHEDULE APPOINTMENT
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
