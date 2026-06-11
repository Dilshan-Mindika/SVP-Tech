@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('employees.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT EMPLOYEE CONSOLE</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Manage personnel profile information, active state, and system credentials linkage</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 relative overflow-hidden">
        <!-- Accent light -->
        <div class="absolute -top-24 -right-24 h-48 w-48 rounded-full bg-cyan-500/5 blur-3xl"></div>

        <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="space-y-6">
            @csrf

            <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">Employee Personnel Profile</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Employee Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Enter full legal name" value="{{ old('name', $employee->name) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-semibold">
                </div>

                <!-- Designation -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Designation Role <span class="text-rose-500">*</span></label>
                    <select name="designation" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        <option value="">Select Role...</option>
                        <option value="Admin" {{ old('designation', $employee->designation) === 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Manager" {{ old('designation', $employee->designation) === 'Manager' ? 'selected' : '' }}>Manager</option>
                        <option value="Cashier" {{ old('designation', $employee->designation) === 'Cashier' ? 'selected' : '' }}>Cashier</option>
                        <option value="Technician" {{ old('designation', $employee->designation) === 'Technician' ? 'selected' : '' }}>Technician</option>
                    </select>
                </div>

                <!-- Salary Base -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Monthly Base Salary (Rs.) <span class="text-rose-500">*</span></label>
                    <input type="number" name="salary_amount" required step="0.01" min="0" placeholder="e.g. 3500.00" value="{{ old('salary_amount', $employee->salary_amount) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-mono font-bold">
                </div>

                <!-- Joining Date -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Joining Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="joining_date" required value="{{ old('joining_date', $employee->joining_date) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Phone -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Mobile / Phone Number</label>
                    <input type="text" name="phone" placeholder="e.g. +1 555-0199" value="{{ old('phone', $employee->phone) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-mono">
                </div>

                <!-- Email -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Work Email Address</label>
                    <input type="email" name="email" placeholder="e.g. employee@cloudtech.com" value="{{ old('email', $employee->email) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-semibold">
                </div>

                <!-- Status -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Employment Status <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Active Employment</option>
                        <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Inactive / Suspended</option>
                    </select>
                </div>
            </div>

            <!-- Login Mapping Section -->
            <div class="pt-4 border-t border-slate-800">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest pb-2">System Credentials Mapping</h2>
                <p class="text-[11px] text-slate-500 leading-normal mb-3">Optional: Associate this personnel file with a registered system login user. This enables access controls for dashboard functions like technicians logs or cashiers checkouts.</p>
                
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Map to System Account</label>
                    <select name="user_id" class="w-full md:w-1/2 bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        <option value="">Do Not Map (Unlinked Personnel Record)</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $employee->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }}) [Role: {{ $user->roleRelation ? $user->roleRelation->name : 'No Role' }}]</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Form Action -->
            <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
                <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-750 text-slate-350 hover:text-slate-200 text-xs font-bold rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-black rounded-lg uppercase tracking-wider transition-all shadow-neon-cyan">
                    SAVE CHANGES
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
