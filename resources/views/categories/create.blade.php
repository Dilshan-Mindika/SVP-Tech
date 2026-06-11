@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('categories.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">CREATE NEW CATEGORY</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Establish a new classification node in the inventory tree</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('categories.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Category Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Mechanical Keyboards" value="{{ old('name') }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
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
                            <option value="{{ $cat->id }}" {{ old('parent_id', $preselectedParentId) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nested_name ?? $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1 italic">Leave empty to initialize as a top-level root department.</p>
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
                        <input type="text" name="icon" placeholder="e.g. fa-keyboard, fa-memory, fa-hdd" value="{{ old('icon', 'fa-tag') }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
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
                <textarea name="description" placeholder="Technical/Logistical description of this category node..." rows="4" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">{{ old('description') }}</textarea>
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
                    SAVE CATEGORY
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
