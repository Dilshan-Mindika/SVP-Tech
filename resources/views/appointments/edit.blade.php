@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT APPOINTMENT</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold">Change appointment details</p>
        </div>
        <a href="{{ route('appointments.index') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-lg transition-colors">
            BACK TO LIST
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('appointments.update', $appointment->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Appointment Number</label>
                <input type="text" value="{{ $appointment->appointment_no }}" class="w-full bg-slate-950 border border-slate-850 text-slate-400 rounded-lg px-3 py-2 text-xs focus:outline-none" disabled>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_name" value="{{ $appointment->customer_name }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Phone <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_phone" value="{{ $appointment->customer_phone }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Email (Optional)</label>
                    <input type="email" name="customer_email" value="{{ $appointment->customer_email }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Appointment Time <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="appointment_time" value="{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('Y-m-d\TH:i') }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                </div>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Status <span class="text-rose-500">*</span></label>
                <select name="status" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>
                    <option value="scheduled" {{ $appointment->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="completed" {{ $appointment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $appointment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Reason for Appointment <span class="text-rose-500">*</span></label>
                <textarea name="reason" rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500" required>{{ $appointment->reason }}</textarea>
            </div>

            <div class="pt-4 flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400">
                    SAVE CHANGES
                </button>
                <a href="{{ route('appointments.index') }}" class="flex-1 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-center font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
