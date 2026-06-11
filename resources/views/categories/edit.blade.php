@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('categories.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT CATEGORY: {{ strtoupper($category->name) }}</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Modify classification properties and hierarchy links</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Category Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Mechanical Keyboards" value="{{ old('name', $category->name) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    @error('name')
                        <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Classification -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Parent Category classification</label>
                    <select name="parent_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        <option value="">None (Top-Level Category)</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nested_name ?? $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1 italic">Note: Circle connections and descendants are automatically filtered out.</p>
                    @error('parent_id')
                        <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon Name -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">FontAwesome Icon Class</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-icons text-xs"></i>
                        </div>
                        <input type="text" name="icon" placeholder="e.g. fa-keyboard, fa-memory, fa-hdd" value="{{ old('icon', $category->icon) }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">Provide any valid FontAwesome 6 free solid class name.</p>
                    @error('icon')
                        <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Category Description</label>
                <textarea name="description" placeholder="Technical/Logistical description of this category node..." rows="4" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 border-t border-slate-800 pt-4">
                <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-slate-850 hover:bg-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
                <button type="submit" class="px-4 py-2 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan">
                    SAVE CHANGES
                </button>
            </div>
        </form>
    </div>

    <!-- Subcategories Section -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-800">
            <h3 class="orbitron-title text-sm font-bold text-slate-200 uppercase tracking-wider">Subcategories under this category</h3>
            @if(Auth::user()->hasPermission('create-categories'))
            <a href="{{ route('categories.create', ['parent_id' => $category->id]) }}" class="px-2.5 py-1.5 bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 hover:bg-cyan-500 hover:text-slate-950 rounded font-bold text-[10px] uppercase tracking-wider transition-all flex items-center gap-1">
                <i class="fa-solid fa-plus text-[9px]"></i> ADD SUBCATEGORY
            </a>
            @endif
        </div>
        
        <div class="overflow-x-auto border border-slate-800/60 rounded-xl">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[9px] bg-slate-950/40">
                        <th class="py-2.5 px-4">Icon</th>
                        <th class="py-2.5 px-4">Subcategory Name</th>
                        <th class="py-2.5 px-4 text-center">Active Items</th>
                        <th class="py-2.5 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-850">
                    @forelse($category->children as $child)
                        <tr class="hover:bg-slate-800/10 transition-colors">
                            <td class="py-2.5 px-4">
                                <div class="h-6 w-6 bg-slate-800 text-cyan-400 rounded flex items-center justify-center">
                                    <i class="fa-solid {{ $child->icon ?: 'fa-tag' }} text-[10px]"></i>
                                </div>
                            </td>
                            <td class="py-2.5 px-4">
                                <span class="font-bold text-slate-200 block">{{ $child->name }}</span>
                                <span class="text-[9px] text-slate-500 block truncate max-w-sm">{{ $child->description }}</span>
                            </td>
                            <td class="py-2.5 px-4 text-center font-bold text-cyan-400 mono-text">
                                {{ $child->total_products_count }}
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('categories.show', $child) }}" class="text-cyan-400 hover:text-cyan-300 transition-colors" title="View subcategory">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('categories.edit', $child) }}" class="text-emerald-400 hover:text-emerald-300 transition-colors" title="Edit subcategory">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete subcategory \'{{ $child->name }}\'?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-400 transition-colors cursor-pointer" title="Delete subcategory">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-500 italic">
                                No subcategories currently mapped to this category node.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
