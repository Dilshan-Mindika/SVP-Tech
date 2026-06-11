@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('products.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">EDIT PRODUCT CONSOLE</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold">Modify catalog profile parameters for SKU: {{ $product->sku }}</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('products.update', $product->id) }}" method="POST" id="editProductForm" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Product Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- SKU -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">SKU / Unique Identifier Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Barcode -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" placeholder="Barcode" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Category -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Category Classification <span class="text-rose-500">*</span></label>
                    <select name="category_id" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->nested_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Brand / Manufacturer <span class="text-rose-500">*</span></label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Buying Price -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Unit Buying Price (Rs.) <span class="text-rose-500">*</span></label>
                    <input type="number" name="buying_price" value="{{ old('buying_price', $product->buying_price) }}" required step="0.01" min="0" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>
 
                <!-- Sale Price -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Unit Selling Price (Rs.) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required step="0.01" min="0" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Whole Sale Price -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Whole Sale Price (Rs.) <span class="text-rose-500">*</span></label>
                    <input type="number" name="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price) }}" required step="0.01" min="0" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Stock (READ ONLY) -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Current Stock Level (Adjust via GRN/Returns)</label>
                    <input type="text" value="{{ $product->stock }} units" disabled class="w-full bg-slate-900 border border-slate-800 text-slate-400 rounded-lg px-3 py-2 text-xs cursor-not-allowed">
                </div>

                <!-- Warranty Months -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Warranty Period (Months) <span class="text-rose-500">*</span></label>
                    <input type="number" name="warranty_months" value="{{ old('warranty_months', $product->warranty_months) }}" required min="0" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Expire Date -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Expire Date</label>
                    <input type="date" name="expire_date" value="{{ old('expire_date', $product->expire_date ? $product->expire_date->format('Y-m-d') : '') }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Product Description</label>
                <textarea name="description" rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Image Upload & Visibility Toggle -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                <div class="md:col-span-2 shrink-0">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Current Image</label>
                    <img src="{{ asset($product->image_path ?: 'images/products/default.jpg') }}" alt="{{ $product->name }}" class="h-16 w-16 object-cover rounded-lg border border-slate-800 bg-slate-950">
                </div>
                <div class="md:col-span-6 flex-grow">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Update Catalog Image</label>
                    <input type="file" name="image" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-850 file:text-cyan-400 hover:file:bg-slate-800 file:cursor-pointer cursor-pointer bg-slate-950 border border-slate-800 rounded-lg p-1.5 focus:outline-none">
                </div>
                <div class="md:col-span-4 flex items-center pt-5">
                    <label class="inline-flex items-center cursor-pointer gap-2">
                        <input type="checkbox" name="is_visible" value="1" {{ old('is_visible', $product->is_visible) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-slate-900 h-4 w-4">
                        <span class="text-xs text-slate-300 font-bold uppercase tracking-wider select-none">Visible on Customer Storefront</span>
                    </label>
                </div>
            </div>

            <!-- Specifications (Dynamic JSON) -->
            <div class="border-t border-slate-800 pt-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block">Technical Specifications</label>
                    <button type="button" id="addSpecBtn" class="text-[10px] bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 hover:bg-cyan-500 hover:text-slate-950 font-bold px-2 py-1 rounded transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-plus"></i>
                        <span>ADD FIELD</span>
                    </button>
                </div>
                
                <div id="specsContainer" class="space-y-2">
                    @if($product->specifications && is_array($product->specifications))
                        @foreach($product->specifications as $key => $val)
                            <div class="flex gap-3 spec-row">
                                <input type="text" placeholder="Specification Name" value="{{ $key }}" class="flex-1 bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500 spec-key">
                                <input type="text" placeholder="Value" value="{{ $val }}" class="flex-1 bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500 spec-val">
                                <button type="button" class="px-2.5 text-slate-500 hover:text-rose-500 transition-colors remove-spec-btn">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="flex gap-3 spec-row">
                            <input type="text" placeholder="Specification Name (e.g. Socket)" class="flex-1 bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500 spec-key">
                            <input type="text" placeholder="Value (e.g. LGA1700)" class="flex-1 bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500 spec-val">
                            <button type="button" class="px-2.5 text-slate-500 hover:text-rose-500 transition-colors remove-spec-btn">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 border-t border-slate-800 pt-4">
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-850 hover:bg-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
                <button type="submit" class="px-4 py-2 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan">
                    UPDATE CATALOG RECORD
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const specsContainer = document.getElementById('specsContainer');
        const addSpecBtn = document.getElementById('addSpecBtn');
        const form = document.getElementById('editProductForm');

        // Add Spec Row
        addSpecBtn.addEventListener('click', function() {
            const row = document.createElement('div');
            row.className = 'flex gap-3 spec-row';
            row.innerHTML = `
                <input type="text" placeholder="Specification Name" class="flex-1 bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500 spec-key">
                <input type="text" placeholder="Value" class="flex-1 bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500 spec-val">
                <button type="button" class="px-2.5 text-slate-500 hover:text-rose-500 transition-colors remove-spec-btn">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            specsContainer.appendChild(row);
            
            row.querySelector('.remove-spec-btn').addEventListener('click', function() {
                row.remove();
            });
        });

        // Wire existing delete buttons
        function wireDeleteButtons() {
            document.querySelectorAll('.remove-spec-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    btn.closest('.spec-row').remove();
                });
            });
        }
        wireDeleteButtons();

        // On Submit: Compile specifications key-value JSON
        form.addEventListener('submit', function() {
            // Remove previous compile-hidden-inputs to avoid duplicate arrays
            form.querySelectorAll('.hidden-spec-input').forEach(el => el.remove());

            const rows = document.querySelectorAll('.spec-row');
            rows.forEach((row, i) => {
                const keyInput = row.querySelector('.spec-key');
                const valInput = row.querySelector('.spec-val');

                if (keyInput && valInput) {
                    const key = keyInput.value.trim();
                    const val = valInput.value.trim();

                    if (key && val) {
                        const hid = document.createElement('input');
                        hid.type = 'hidden';
                        hid.className = 'hidden-spec-input';
                        hid.name = `specifications[${key}]`;
                        hid.value = val;
                        form.appendChild(hid);
                    }
                }
            });
        });
    });
</script>
@endsection
