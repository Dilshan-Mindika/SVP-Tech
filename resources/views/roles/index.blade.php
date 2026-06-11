@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">ROLES</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans font-medium">Manage user roles and permissions</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('permissions.index') }}" class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-330 hover:text-slate-100 text-xs font-bold transition-all flex items-center gap-2">
                <i class="fa-solid fa-list-check"></i>
                <span>PERMISSIONS</span>
            </a>
            <a href="{{ route('roles.create') }}" class="px-4 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-sm transition-all hover:bg-cyan-400 shadow-neon-cyan flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>ADD ROLE</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Stat Card 1 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Roles</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text font-black">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Configured user roles</span>
        </div>
        
        <!-- Stat Card 2 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #fb7185; opacity: 0.15;">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Available Permissions</span>
            <h3 class="text-xl font-extrabold text-rose-400 mt-1 mono-text font-black">{{ $stats['total_permissions'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">System capability flags</span>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Active Roles</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text font-black">{{ $stats['roles_with_users'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Roles with active users</span>
        </div>

        <!-- Stat Card 4 -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group transition-all hover:border-slate-700/80">
            <div class="absolute -right-4 -bottom-4 text-6xl transition-transform group-hover:scale-110" style="color: #fbbf24; opacity: 0.15;">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">User Assignments</span>
            <h3 class="text-xl font-extrabold text-amber-400 mt-1 mono-text font-black">{{ $stats['total_assignments'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Total account links</span>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('roles.index') }}" method="GET" class="flex gap-4 items-center">
            <div class="flex-grow relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by role name..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg transition-colors shrink-0">
                Search
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                        <th class="py-3.5 px-6">Role Name</th>
                        <th class="py-3.5 px-6">Description</th>
                        <th class="py-3.5 px-6 text-center">Assigned Users</th>
                        <th class="py-3.5 px-6">Date Created</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($roles as $role)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-slate-200">{{ $role->name }}</td>
                            <td class="py-3.5 px-6 text-slate-300">{{ $role->description ?? 'N/A' }}</td>
                            <td class="py-3.5 px-6 text-center font-bold text-cyan-400 mono-text">{{ $role->users_count }}</td>
                            <td class="py-3.5 px-6 text-slate-400">{{ $role->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('roles.edit', $role->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="Edit Role Details">
                                        <i class="fa-solid fa-user-gear"></i>
                                    </a>
                                    
                                    @if($role->name !== 'Admin' && $role->name !== 'Super Admin')
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-500 hover:text-rose-500 rounded transition-all" title="Delete Role">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-user-shield text-2xl mb-2 block opacity-40"></i>
                                <span>No roles found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($roles->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
