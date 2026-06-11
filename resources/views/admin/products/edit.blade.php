@extends('layouts.shop')

@section('title', 'Neuronet | Edit Product')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Heading and Action -->
    <div class="flex justify-between items-center gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold font-orbitron text-white tracking-widest uppercase">
                RECONFIGURE HARDWARE MODULE
            </h1>
            <div class="w-24 h-1 bg-cyber-cyan mt-2 shadow-neon-cyan"></div>
            <p class="text-xs text-gray-500 mt-2 font-mono">ID: {{ $product->id }} | SKU: {{ $product->slug }}</p>
        </div>

        <a href="{{ route('admin.products') }}" class="px-4 py-2.5 rounded-lg border border-cyber-border bg-cyber-card text-gray-400 hover:text-cyber-cyan hover:border-cyber-cyan/50 font-orbitron text-xs font-bold transition duration-150">
            <i class="fa-solid fa-angle-left mr-2"></i> INVENTORY LIST
        </a>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-cyber-card/45 border border-cyber-border rounded-2xl p-6 md:p-8 space-y-6 backdrop-blur-sm">
        @csrf

        <!-- Name & Brand Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label for="name" class="block text-xs font-orbitron font-bold text-gray-400">PRODUCT NAME <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" required value="{{ old('name', $product->name) }}"
                       class="w-full bg-cyber-dark border border-cyber-border focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan text-gray-200 rounded-xl py-3 px-4 transition duration-200">
                @error('name')
                    <span class="text-xs text-red-400 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="brand" class="block text-xs font-orbitron font-bold text-gray-400">BRAND NAME <span class="text-rose-500">*</span></label>
                <input type="text" name="brand" id="brand" required value="{{ old('brand', $product->brand) }}"
                       class="w-full bg-cyber-dark border border-cyber-border focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan text-gray-200 rounded-xl py-3 px-4 transition duration-200">
                @error('brand')
                    <span class="text-xs text-red-400 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Category, Price, Stock Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-2">
                <label for="category_id" class="block text-xs font-orbitron font-bold text-gray-400">HARDWARE CATEGORY <span class="text-rose-500">*</span></label>
                <select name="category_id" id="category_id" required 
                        class="w-full bg-cyber-dark border border-cyber-border focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan text-gray-300 rounded-xl py-3 px-4 transition duration-200">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->nested_name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <span class="text-xs text-red-400 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-orbitron font-bold text-gray-400">UNIT PRICE (Rs.) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" name="price" id="price" required value="{{ old('price', $product->price) }}"
                       class="w-full bg-cyber-dark border border-cyber-border focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan text-gray-200 rounded-xl py-3 px-4 transition duration-200">
                @error('price')
                    <span class="text-xs text-red-400 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-2">
                <label for="stock" class="block text-xs font-orbitron font-bold text-gray-400">STOCK UNITS <span class="text-rose-500">*</span></label>
                <input type="number" name="stock" id="stock" required value="{{ old('stock', $product->stock) }}"
                       class="w-full bg-cyber-dark border border-cyber-border focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan text-gray-200 rounded-xl py-3 px-4 transition duration-200">
                @error('stock')
                    <span class="text-xs text-red-400 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Image Upload and Preview -->
        <div class="space-y-2">
            <label class="block text-xs font-orbitron font-bold text-gray-400">PRODUCT IMAGE</label>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                <!-- Preview current image if exists -->
                <div class="md:col-span-1 w-24 h-24 rounded-xl border border-cyber-border bg-cyber-dark flex items-center justify-center text-gray-500 overflow-hidden mx-auto md:mx-0">
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                    @else
                        <i class="fa-solid fa-box text-3xl"></i>
                    @endif
                </div>

                <!-- Input to upload new -->
                <div class="md:col-span-3 w-full bg-cyber-dark border border-cyber-border focus-within:border-cyber-cyan rounded-xl p-4 transition duration-200 flex flex-col items-center justify-center text-gray-400 gap-2 border-dashed">
                    <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                    <input type="file" name="image" id="image" class="block text-xs text-gray-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border file:border-cyber-border file:text-xs file:font-semibold file:bg-cyber-dark/80 file:text-gray-300 file:cursor-pointer hover:file:border-cyber-cyan hover:file:text-cyber-cyan transition duration-200">
                    <span class="text-[9px] text-gray-500">To replace image, choose a new one. Max 2MB.</span>
                </div>
            </div>
            @error('image')
                <span class="text-xs text-red-400 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Description -->
        <div class="space-y-2">
            <label for="description" class="block text-xs font-orbitron font-bold text-gray-400">PRODUCT DESCRIPTION <span class="text-rose-500">*</span></label>
            <textarea name="description" id="description" rows="5" required
                      class="w-full bg-cyber-dark border border-cyber-border focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan text-gray-200 rounded-xl py-3 px-4 transition duration-200">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <span class="text-xs text-red-400 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Specifications -->
        <div class="space-y-4">
            <div class="flex justify-between items-center border-b border-cyber-border/60 pb-2">
                <label class="block text-xs font-orbitron font-bold text-gray-400">TECHNICAL SPECIFICATIONS</label>
                <button type="button" id="add-spec-btn" class="text-xs font-orbitron text-cyber-cyan hover:text-white flex items-center gap-1.5 transition duration-150">
                    <i class="fa-solid fa-plus-circle"></i> ADD SPEC FIELD
                </button>
            </div>

            <!-- Spec rows container -->
            <div id="specs-container" class="space-y-3">
                @php
                    $specs = $product->specifications ?? [];
                @endphp
                @forelse($specs as $key => $val)
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center spec-row">
                        <input type="text" name="specs_keys[]" value="{{ $key }}" placeholder="Key" class="sm:col-span-5 bg-cyber-dark border border-cyber-border focus:border-cyber-cyan text-gray-200 rounded-xl py-2 px-3 text-sm transition duration-200">
                        <input type="text" name="specs_values[]" value="{{ $val }}" placeholder="Value" class="sm:col-span-6 bg-cyber-dark border border-cyber-border focus:border-cyber-cyan text-gray-200 rounded-xl py-2 px-3 text-sm transition duration-200">
                        <button type="button" class="sm:col-span-1 p-2 text-gray-500 hover:text-red-400 hover:border-red-500/30 border border-transparent rounded-lg remove-spec-btn transition duration-150">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                @empty
                    <!-- Fallback single empty row -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center spec-row">
                        <input type="text" name="specs_keys[]" placeholder="Key" class="sm:col-span-5 bg-cyber-dark border border-cyber-border focus:border-cyber-cyan text-gray-200 rounded-xl py-2 px-3 text-sm transition duration-200">
                        <input type="text" name="specs_values[]" placeholder="Value" class="sm:col-span-6 bg-cyber-dark border border-cyber-border focus:border-cyber-cyan text-gray-200 rounded-xl py-2 px-3 text-sm transition duration-200">
                        <button type="button" class="sm:col-span-1 p-2 text-gray-500 hover:text-red-400 hover:border-red-500/30 border border-transparent rounded-lg remove-spec-btn transition duration-150">
                            <i class="fa-solid fa-trash-can text-sm"></i>
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Options -->
        <div class="flex flex-col sm:flex-row gap-6 border-t border-cyber-border/40 pt-6">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                       class="text-cyber-cyan focus:ring-cyber-cyan border-cyber-border bg-cyber-dark rounded">
                <label for="is_featured" class="text-xs font-orbitron font-bold text-gray-300 cursor-pointer">FEATURED ON STOREFRONT</label>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_visible" id="is_visible" value="1" {{ old('is_visible', $product->is_visible) ? 'checked' : '' }}
                       class="text-cyber-cyan focus:ring-cyber-cyan border-cyber-border bg-cyber-dark rounded">
                <label for="is_visible" class="text-xs font-orbitron font-bold text-gray-300 cursor-pointer">VISIBLE ON STOREFRONT</label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit" class="w-full py-4 bg-cyber-cyan hover:bg-cyber-cyan/80 text-cyber-dark font-extrabold font-orbitron rounded-xl transition duration-300 shadow-neon-cyan hover:shadow-neon-cyan-lg transform hover:-translate-y-0.5 text-center flex items-center justify-center gap-2">
                UPDATE PRODUCT HARDWARE CONFIGURATION <i class="fa-solid fa-microchip animate-pulse"></i>
            </button>
        </div>
    </form>
</div>

<!-- Scripts for dynamic specifications -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const specsContainer = document.getElementById('specs-container');
        const addSpecBtn = document.getElementById('add-spec-btn');

        // Add Spec Row
        addSpecBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'grid grid-cols-1 sm:grid-cols-12 gap-3 items-center spec-row';
            newRow.innerHTML = `
                <input type="text" name="specs_keys[]" placeholder="Key" class="sm:col-span-5 bg-cyber-dark border border-cyber-border focus:border-cyber-cyan text-gray-200 rounded-xl py-2 px-3 text-sm transition duration-200">
                <input type="text" name="specs_values[]" placeholder="Value" class="sm:col-span-6 bg-cyber-dark border border-cyber-border focus:border-cyber-cyan text-gray-200 rounded-xl py-2 px-3 text-sm transition duration-200">
                <button type="button" class="sm:col-span-1 p-2 text-gray-500 hover:text-red-400 hover:border-red-500/30 border border-transparent rounded-lg remove-spec-btn transition duration-150">
                    <i class="fa-solid fa-trash-can text-sm"></i>
                </button>
            `;
            specsContainer.appendChild(newRow);
        });

        // Remove Spec Row
        specsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-spec-btn')) {
                const row = e.target.closest('.spec-row');
                row.remove();
            }
        });
    });
</script>
@endsection
