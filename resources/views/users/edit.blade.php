@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT USER: {{ $user->name }}</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Modify user account details & credentials</p>
        </div>
        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 text-xs transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">User Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Enter full name" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                </div>

                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Enter email address" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">System Role <span class="text-rose-500">*</span></label>
                <select name="role_id" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    <option value="">Select a system role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }} ({{ $role->description }})</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-slate-950/60 border border-slate-800/80 p-4 rounded-lg space-y-3">
                <div class="flex items-center gap-2 text-cyan-400 text-xs font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-lock"></i>
                    <span>Reset Password (Optional)</span>
                </div>
                <p class="text-[10px] text-slate-400">Leave these fields blank if you do not wish to change the password.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">New Password</label>
                        <input type="password" name="password" placeholder="Enter new password" class="w-full bg-slate-900 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    </div>

                    <div>
                        <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm new password" class="w-full bg-slate-900 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest hover:bg-cyan-400 transition-colors shadow-neon-cyan">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
