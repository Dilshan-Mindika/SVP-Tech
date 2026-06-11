@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">PERMISSIONS</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans font-medium">Manage fine-grained access rights</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-300 hover:text-slate-100 text-xs font-bold transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Roles</span>
            </a>
            <a href="{{ route('permissions.create') }}" class="px-4 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-sm transition-all hover:bg-cyan-400 shadow-neon-cyan flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>ADD PERMISSION</span>
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
        <form action="{{ route('permissions.index') }}" method="GET" class="flex gap-4 items-center">
            <div class="flex-grow relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or module..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
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
                        <th class="py-3.5 px-6">Permission Name</th>
                        <th class="py-3.5 px-6">Module Name</th>
                        <th class="py-3.5 px-6">Date Created</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($permissions as $perm)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-slate-200">{{ $perm->name }}</td>
                            <td class="py-3.5 px-6 text-cyan-400 font-semibold">{{ $perm->module }}</td>
                            <td class="py-3.5 px-6 text-slate-400">{{ $perm->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('permissions.edit', $perm->id) }}" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-300 rounded hover:text-cyan-400 transition-all" title="Edit Permission">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    
                                    <form action="{{ route('permissions.destroy', $perm->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this permission? This could affect role mapping.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-850 hover:bg-slate-800 text-slate-500 hover:text-rose-500 rounded transition-all" title="Delete Permission">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-600">
                                <i class="fa-solid fa-list-check text-2xl mb-2 block opacity-40"></i>
                                <span>No permissions found.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($permissions->hasPages())
            <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                {{ $permissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
