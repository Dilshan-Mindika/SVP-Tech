@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('products.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">LOG NEW PRODUCT</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold">Initialize catalog profile and initial inventory</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('products.store') }}" method="POST" id="createProductForm" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Product Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Core i7-13700K Desktop Processor" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- SKU -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">SKU / Unique Identifier Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="sku" required placeholder="e.g. CPU-INT-13700K" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Barcode -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Barcode</label>
                    <input type="text" name="barcode" placeholder="Scan or enter barcode" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Category -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Category Classification <span class="text-rose-500">*</span></label>
                    <select name="category_id" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nested_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Brand / Manufacturer <span class="text-rose-500">*</span></label>
                    <input type="text" name="brand" required placeholder="e.g. Intel" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Buying Price -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Unit Buying Price (Rs.) <span class="text-rose-500">*</span></label>
                    <input type="number" name="buying_price" required step="0.01" min="0" placeholder="0.00" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Sale Price -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Unit Selling Price (Rs.) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" required step="0.01" min="0" placeholder="0.00" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Whole Sale Price -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Whole Sale Price (Rs.) <span class="text-rose-500">*</span></label>
                    <input type="number" name="wholesale_price" required step="0.01" min="0" value="0.00" placeholder="0.00" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Initial Stock -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Initial Stock Count <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" required min="0" value="0" placeholder="0" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                    <p class="text-[10px] text-slate-500 mt-1 italic">Note: Serial numbers (e.g. SKU-0001) will be auto-generated for this stock.</p>
                </div>

                <!-- Warranty Months -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Warranty Period (Months) <span class="text-rose-500">*</span></label>
                    <input type="number" name="warranty_months" required min="0" value="12" placeholder="12" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Expire Date -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Expire Date</label>
                    <input type="date" name="expire_date" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Product Description</label>
                <textarea name="description" placeholder="Technical description of the unit..." rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors"></textarea>
            </div>

            <!-- Image Upload & Visibility Toggle -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Catalog Image</label>
                    <input type="file" name="image" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-850 file:text-cyan-400 hover:file:bg-slate-800 file:cursor-pointer cursor-pointer bg-slate-950 border border-slate-800 rounded-lg p-1.5 focus:outline-none">
                </div>
                <div class="flex items-center pt-5">
                    <label class="inline-flex items-center cursor-pointer gap-2">
                        <input type="checkbox" name="is_visible" value="1" checked class="rounded bg-slate-950 border-slate-800 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-slate-900 h-4 w-4">
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
                    <div class="flex gap-3 spec-row">
                        <input type="text" placeholder="Specification Name (e.g. Socket)" class="flex-1 bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500 spec-key">
                        <input type="text" placeholder="Value (e.g. LGA1700)" class="flex-1 bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500 spec-val">
                        <button type="button" class="px-2.5 text-slate-500 hover:text-rose-500 transition-colors remove-spec-btn">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 border-t border-slate-800 pt-4">
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-850 hover:bg-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
                <button type="submit" class="px-4 py-2 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan">
                    SAVE PRODUCT TO CATALOG
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const specsContainer = document.getElementById('specsContainer');
        const addSpecBtn = document.getElementById('addSpecBtn');
        const form = document.getElementById('createProductForm');

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
