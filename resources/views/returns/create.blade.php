@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('returns.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">PROCESS NEW RETURN</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Route customer returns (adds to stock) or supplier returns (deducts from stock) with serial status updates</p>
        </div>
    </div>

    <form action="{{ route('returns.store') }}" method="POST" id="returnsForm" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf

        <!-- Left Column: Header and Parameters -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">Return Header</h2>

                <!-- Return Type Router -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Return Routing Type <span class="text-rose-500">*</span></label>
                    <select name="type" id="returnTypeSelect" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        <option value="customer_return">Customer Return (Restocks Inventory)</option>
                        <option value="supplier_return">Supplier Return (Deducts Inventory)</option>
                    </select>
                </div>

                <!-- Linked Invoice (visible only for customer return) -->
                <div id="invoiceGroup">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Linked Customer Invoice <span class="text-rose-500">*</span></label>
                    <select name="invoice_id" id="invoiceSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        <option value="">Select Customer Invoice...</option>
                        @foreach($invoices as $inv)
                            <option value="{{ $inv->id }}">{{ $inv->invoice_number }} - Client: {{ $inv->customer ? $inv->customer->name : 'Walk-in' }} (Rs. {{ number_format($inv->total, 2) }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Linked Supplier (visible only for supplier return) -->
                <div id="supplierGroup" class="hidden">
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Linked Supplier Partner <span class="text-rose-500">*</span></label>
                    <select name="supplier_id" id="supplierSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        <option value="">Select Supplier...</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }} ({{ $supplier->name }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Refund Amount -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Refund / Credit Amount (Rs.) <span class="text-rose-500">*</span></label>
                    <input type="number" name="refund_amount" required step="0.01" min="0" value="0.00" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-mono">
                </div>

                <!-- Reason -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Reason for Return <span class="text-rose-500">*</span></label>
                    <textarea name="reason" required placeholder="Log defect report, customer change of mind, supplier batch RMA..." rows="3" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan">
                        COMMIT RETURN TICKET
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Line Items -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                    <h2 class="orbitron-title text-xs font-bold text-cyan-400 uppercase tracking-widest">Return Items</h2>
                    <button type="button" id="addItemRowBtn" class="text-[10px] bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 hover:bg-cyan-500 hover:text-slate-950 font-bold px-2 py-1 rounded transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-plus"></i>
                        <span>ADD ITEM LINE</span>
                    </button>
                </div>

                <!-- Products Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-widest font-semibold text-[9px]">
                                <th class="pb-2 pr-4 w-1/2">Product Item <span class="text-rose-500">*</span></th>
                                <th class="pb-2 px-2 w-20">Qty <span class="text-rose-500">*</span></th>
                                <th class="pb-2 px-2 w-48">Serial Number</th>
                                <th class="pb-2 pl-4 text-center w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="returnItemsTableBody" class="divide-y divide-slate-850">
                            <!-- Dynamic Item Rows Will Load Here -->
                        </tbody>
                    </table>
                </div>

                <div id="emptyState" class="py-8 text-center text-slate-600">
                    <i class="fa-solid fa-rotate-left text-2xl mb-2 block opacity-40"></i>
                    <span>No return lines added yet. Click "Add Item Line" to begin.</span>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Product list JSON script -->
<script id="products-data" type="application/json">
    {!! json_encode($products) !!}
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('returnTypeSelect');
        const invoiceGroup = document.getElementById('invoiceGroup');
        const supplierGroup = document.getElementById('supplierGroup');
        const invoiceSelect = document.getElementById('invoiceSelect');
        const supplierSelect = document.getElementById('supplierSelect');

        const tableBody = document.getElementById('returnItemsTableBody');
        const addRowBtn = document.getElementById('addItemRowBtn');
        const emptyState = document.getElementById('emptyState');
        const products = JSON.parse(document.getElementById('products-data').textContent);
        
        let rowIndex = 0;

        // Toggle Invoice vs Supplier group based on type selected
        typeSelect.addEventListener('change', function() {
            if (typeSelect.value === 'customer_return') {
                invoiceGroup.classList.remove('hidden');
                supplierGroup.classList.add('hidden');
                invoiceSelect.required = true;
                supplierSelect.required = false;
                supplierSelect.value = '';
            } else {
                invoiceGroup.classList.add('hidden');
                supplierGroup.classList.remove('hidden');
                invoiceSelect.required = false;
                supplierSelect.required = true;
                invoiceSelect.value = '';
            }
        });

        // Set initial validation
        invoiceSelect.required = true;

        function checkEmptyState() {
            if (tableBody.children.length === 0) {
                emptyState.style.display = 'block';
            } else {
                emptyState.style.display = 'none';
            }
        }

        function createRow() {
            const row = document.createElement('tr');
            row.className = 'hover:bg-slate-800/10 transition-colors return-row';
            row.dataset.index = rowIndex;
            
            // Build options
            let optionsHtml = '<option value="">Select Catalog Product...</option>';
            products.forEach(p => {
                optionsHtml += `<option value="${p.id}">${p.name} [SKU: ${p.sku}]</option>`;
            });

            row.innerHTML = `
                <td class="py-3.5 pr-4">
                    <select name="items[${rowIndex}][product_id]" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 product-select">
                        ${optionsHtml}
                    </select>
                </td>
                <td class="py-3.5 px-2">
                    <input type="number" name="items[${rowIndex}][quantity]" required min="1" value="1" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 quantity-input mono-text">
                </td>
                <td class="py-3.5 px-2">
                    <input type="text" name="items[${rowIndex}][serial_number]" placeholder="S/N (Optional)" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 mono-text uppercase">
                </td>
                <td class="py-3.5 pl-4 text-center">
                    <button type="button" class="text-slate-500 hover:text-rose-500 transition-colors remove-row-btn">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;

            tableBody.appendChild(row);
            rowIndex++;
            checkEmptyState();
            
            const removeBtn = row.querySelector('.remove-row-btn');
            removeBtn.addEventListener('click', function() {
                row.remove();
                checkEmptyState();
            });
        }

        addRowBtn.addEventListener('click', createRow);

        // Add initial row
        createRow();
    });
</script>
@endsection
