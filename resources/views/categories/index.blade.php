@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="orbitron-title text-2xl font-black text-slate-100 tracking-wider">CATEGORIES</h1>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-semibold font-sans font-sans">Organize your inventory catalog with nested subcategories</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            @if(Auth::user()->hasPermission('create-categories'))
            <a href="{{ route('categories.create') }}" class="px-3.5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs transition-all hover:bg-cyan-400 shadow-neon-cyan hover:shadow-neon-cyan-lg flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>ADD CATEGORY</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Statistical KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #22d3ee; opacity: 0.15;">
                <i class="fa-solid fa-tags"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Total Categories</span>
            <h3 class="text-xl font-extrabold text-cyan-400 mt-1 mono-text">{{ $stats['total_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Nesting levels supported</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #34d399; opacity: 0.15;">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Top-Level (Departments)</span>
            <h3 class="text-xl font-extrabold text-emerald-400 mt-1 mono-text">{{ $stats['top_level_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Parent categories without a master</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #f59e0b; opacity: 0.15;">
                <i class="fa-solid fa-folder-minus"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Subcategories</span>
            <h3 class="text-xl font-extrabold text-amber-400 mt-1 mono-text">{{ $stats['subcategories_count'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Nested under parent departments</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-5xl transition-transform group-hover:scale-110" style="color: #f43f5e; opacity: 0.15;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block">Orphaned Products</span>
            <h3 class="text-xl font-extrabold text-rose-500 mt-1 mono-text">{{ $stats['orphaned_products_check'] }}</h3>
            <span class="text-[9px] text-slate-500 font-semibold block mt-1">Products without assigned category</span>
        </div>
    </div>

    <!-- Toggle Layout Panel -->
    <div class="flex border-b border-slate-800">
        <button onclick="switchView('tree')" id="tab-tree" class="px-6 py-3 border-b-2 border-cyan-400 text-cyan-400 font-orbitron font-bold text-xs uppercase tracking-wider transition-all cursor-pointer">
            <i class="fa-solid fa-sitemap mr-1.5"></i> Visual Tree
        </button>
        <button onclick="switchView('table')" id="tab-table" class="px-6 py-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 font-orbitron font-bold text-xs uppercase tracking-wider transition-all cursor-pointer">
            <i class="fa-solid fa-table-list mr-1.5"></i> Flat Table & Search
        </button>
    </div>

    <!-- 1. Visual Tree View -->
    <div id="view-tree" class="space-y-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h3 class="orbitron-title text-sm font-bold text-slate-300 mb-6 tracking-wide uppercase">Interactive Catalog Tree</h3>
            
            <div class="space-y-4">
                @forelse($categoryTree as $parent)
                    <div class="border border-slate-800/60 rounded-xl bg-slate-950/40 p-4">
                        <!-- Top-level Category Row -->
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid {{ $parent->icon ?: 'fa-folder' }} text-sm"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-200 text-sm flex items-center gap-2">
                                        {{ $parent->name }}
                                        <span class="px-2 py-0.5 bg-cyan-500/10 text-cyan-400 text-[9px] font-semibold rounded uppercase tracking-wider">Parent</span>
                                    </span>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">{{ $parent->description ?: 'No description provided.' }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <span class="mono-text text-[11px] text-slate-400 bg-slate-900 border border-slate-800 rounded px-2.5 py-1">
                                    {{ $parent->total_products_count }} Products (Recursive)
                                </span>
                                
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('categories.show', $parent) }}" class="p-1.5 hover:text-cyan-400 text-slate-500 transition-colors" title="View Detail Profile">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    @if(Auth::user()->hasPermission('update-categories'))
                                    <a href="{{ route('categories.edit', $parent) }}" class="p-1.5 hover:text-emerald-400 text-slate-500 transition-colors" title="Edit Category">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    @endif
                                    @if(Auth::user()->hasPermission('delete-categories'))
                                    <form action="{{ route('categories.destroy', $parent) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete category \'{{ $parent->name }}\'? Children will be set to top-level.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 hover:text-rose-500 text-slate-500 transition-colors cursor-pointer" title="Delete Category">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Nested Child Categories -->
                        @if($parent->children->isNotEmpty())
                            <div class="mt-4 ml-8 border-l-2 border-slate-800 pl-4 space-y-3">
                                @foreach($parent->children as $child)
                                    <div class="flex items-center justify-between gap-4 py-2 bg-slate-900/20 px-3 rounded-lg border border-slate-850 hover:border-slate-800 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="h-7 w-7 bg-slate-800 text-slate-400 rounded-md flex items-center justify-center">
                                                <i class="fa-solid {{ $child->icon ?: 'fa-tag' }} text-xs"></i>
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-300 text-xs">{{ $child->name }}</span>
                                                <span class="text-[9px] text-slate-500 block truncate max-w-sm mt-0.5">{{ $child->description }}</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-4">
                                            <span class="mono-text text-[10px] text-slate-500">
                                                {{ $child->total_products_count }} Products
                                            </span>
                                            
                                            <div class="flex items-center gap-1">
                                                <a href="{{ route('categories.show', $child) }}" class="p-1.5 hover:text-cyan-400 text-slate-500 transition-colors" title="View Detail Profile">
                                                    <i class="fa-solid fa-eye text-[11px]"></i>
                                                </a>
                                                @if(Auth::user()->hasPermission('update-categories'))
                                                <a href="{{ route('categories.edit', $child) }}" class="p-1.5 hover:text-emerald-400 text-slate-500 transition-colors" title="Edit Category">
                                                    <i class="fa-solid fa-pen text-[11px]"></i>
                                                </a>
                                                @endif
                                                @if(Auth::user()->hasPermission('delete-categories'))
                                                <form action="{{ route('categories.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete category \'{{ $child->name }}\'?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 hover:text-rose-500 text-slate-500 transition-colors cursor-pointer" title="Delete Category">
                                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-10">
                        <i class="fa-solid fa-tags text-4xl text-slate-700 mb-3"></i>
                        <p class="text-slate-400 text-xs font-semibold">No categories logged in the database.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 2. Flat Search Table View -->
    <div id="view-table" class="space-y-4 hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
            <form action="{{ route('categories.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <input type="hidden" name="view" value="table">
                <div class="flex-grow w-full md:max-w-md relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Name, Slug or Description..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-wider hover:bg-cyan-400 transition-colors">
                        SEARCH
                    </button>
                    <a href="{{ route('categories.index', ['view' => 'table']) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-lg text-xs transition-colors flex items-center">
                        RESET
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[10px]">
                            <th class="py-3.5 px-6">Icon</th>
                            <th class="py-3.5 px-6">Name</th>
                            <th class="py-3.5 px-6">Slug</th>
                            <th class="py-3.5 px-6">Parent Classification</th>
                            <th class="py-3.5 px-6 text-center">Direct Products</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-850">
                        @forelse($flatCategories as $cat)
                            <tr class="hover:bg-slate-800/10 transition-colors">
                                <td class="py-3.5 px-6">
                                    <div class="h-8 w-8 bg-slate-800 text-cyan-400 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid {{ $cat->icon ?: 'fa-tag' }}"></i>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <span class="font-bold text-slate-200 block">{{ $cat->name }}</span>
                                    <span class="text-[10px] text-slate-500 block truncate max-w-xs mt-0.5">{{ $cat->description }}</span>
                                </td>
                                <td class="py-3.5 px-6 font-mono text-slate-400">{{ $cat->slug }}</td>
                                <td class="py-3.5 px-6">
                                    @if($cat->parent)
                                        <span class="px-2 py-1 bg-slate-800 border border-slate-700/80 rounded text-slate-300 font-semibold text-[10px]">
                                            {{ $cat->parent->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 italic">None (Root)</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-center font-bold text-cyan-400 mono-text">
                                    {{ $cat->products_count }}
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('categories.show', $cat) }}" class="text-cyan-400 hover:text-cyan-300 transition-colors" title="View details">
                                            <i class="fa-solid fa-eye text-sm"></i>
                                        </a>
                                        @if(Auth::user()->hasPermission('update-categories'))
                                        <a href="{{ route('categories.edit', $cat) }}" class="text-emerald-400 hover:text-emerald-300 transition-colors" title="Edit details">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </a>
                                        @endif
                                        @if(Auth::user()->hasPermission('delete-categories'))
                                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete category \'{{ $cat->name }}\'?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-400 transition-colors cursor-pointer" title="Delete category">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-500">
                                    No categories match the search query.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($flatCategories->hasPages())
                <div class="px-6 py-4 bg-slate-900 border-t border-slate-800">
                    {{ $flatCategories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function switchView(view) {
        const treeView = document.getElementById('view-tree');
        const tableView = document.getElementById('view-table');
        const tabTree = document.getElementById('tab-tree');
        const tabTable = document.getElementById('tab-table');

        if (view === 'tree') {
            treeView.classList.remove('hidden');
            tableView.classList.add('hidden');
            tabTree.className = "px-6 py-3 border-b-2 border-cyan-400 text-cyan-400 font-orbitron font-bold text-xs uppercase tracking-wider transition-all cursor-pointer";
            tabTable.className = "px-6 py-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 font-orbitron font-bold text-xs uppercase tracking-wider transition-all cursor-pointer";
        } else {
            treeView.classList.add('hidden');
            tableView.classList.remove('hidden');
            tabTree.className = "px-6 py-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 font-orbitron font-bold text-xs uppercase tracking-wider transition-all cursor-pointer";
            tabTable.className = "px-6 py-3 border-b-2 border-cyan-400 text-cyan-400 font-orbitron font-bold text-xs uppercase tracking-wider transition-all cursor-pointer";
        }
    }

    // Keep active view on load
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const view = urlParams.get('view');
        if (view === 'table' || "{{ request('search') }}" !== '') {
            switchView('table');
        }
    });
</script>
@endsection
