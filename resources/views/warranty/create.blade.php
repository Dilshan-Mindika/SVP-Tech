@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3 pb-4 border-b border-slate-800">
        <a href="{{ route('warranty.index') }}" class="p-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">FILE WARRANTY CLAIM</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold font-sans">Open an RMA ticket for client equipment service or vendor exchange</p>
        </div>
    </div>

    <!-- Form Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <form action="{{ route('warranty.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <!-- Customer Selection -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer / claimant <span class="text-rose-500">*</span></label>
                    <select name="customer_id" id="customer_select" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors">
                        <option value="">Select Customer...</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ (old('customer_id') == $cust->id || (isset($selectedInvoice) && $selectedInvoice->customer_id == $cust->id)) ? 'selected' : '' }}>{{ $cust->name }} ({{ $cust->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Linked Invoice -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Linked Purchase Invoice <span class="text-rose-500">*</span></label>
                    <select name="invoice_id" id="invoice_select" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors" {{ count($customerInvoices) === 0 ? 'disabled' : '' }}>
                        <option value="">Select Purchased Invoice...</option>
                        @foreach($customerInvoices as $inv)
                            <option value="{{ $inv->id }}" {{ (old('invoice_id') == $inv->id || $selectedInvoiceId == $inv->id) ? 'selected' : '' }}>{{ $inv->invoice_number }} - Total: Rs. {{ number_format($inv->total, 2) }} ({{ \Carbon\Carbon::parse($inv->created_at)->format('Y-m-d') }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Defective Product Item -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Defective Product Item <span class="text-rose-500">*</span></label>
                    <select name="product_id" id="product_select" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors" {{ count($invoiceItems) === 0 ? 'disabled' : '' }}>
                        <option value="">Select Catalog Item...</option>
                        @if($selectedInvoice)
                            @foreach($invoiceItems as $item)
                                <option value="{{ $item->product_id }}" {{ (old('product_id') == $item->product_id || $selectedProductId == $item->product_id) ? 'selected' : '' }}>
                                    {{ $item->product->name }} [SKU: {{ $item->product->sku }}]
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Serial Number -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Device Unit Serial Number (S/N)</label>
                    <input type="text" name="serial_number" id="serial_input" value="{{ old('serial_number', $selectedSerialNumber) }}" placeholder="e.g. CPU-INT-13700K-0021" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-mono uppercase">
                    <p class="text-[10px] text-slate-500 mt-1 italic">If present, this unit's status in the registry will transition to "Under Repair".</p>
                </div>

                <!-- Claim Date -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Claim Intake Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="claim_date" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors font-mono">
                </div>

                <!-- Fault Description -->
                <div>
                    <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Defect symptoms & Issue Description <span class="text-rose-500">*</span></label>
                    <textarea name="issue_description" required placeholder="Detail the fault symptoms reported by customer (e.g. Blue screens under load, CPU overheating)..." rows="4" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors"></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 border-t border-slate-800 pt-4">
                <a href="{{ route('warranty.index') }}" class="px-4 py-2 bg-slate-850 hover:bg-slate-800 text-slate-300 font-bold rounded-lg text-xs transition-colors">
                    CANCEL
                </a>
                <button type="submit" class="px-4 py-2 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan">
                    INITIATE CLAIM TICKET
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_select');
    const invoiceSelect = document.getElementById('invoice_select');
    const productSelect = document.getElementById('product_select');
    const serialInput = document.getElementById('serial_input');

    // Keep a map of product_id -> serial_number for the loaded invoice items
    let invoiceItemsMap = {};

    // Initialize map if invoice is pre-selected
    @if($selectedInvoice)
        @foreach($invoiceItems as $item)
            invoiceItemsMap["{{ $item->product_id }}"] = "{{ $item->serial_number }}";
        @endforeach
    @endif

    // Handle Customer Selection Change
    customerSelect.addEventListener('change', function() {
        const customerId = this.value;
        if (!customerId) {
            clearCustomerDependentFields();
            return;
        }

        // Fetch customer invoices
        invoiceSelect.disabled = true;
        invoiceSelect.innerHTML = '<option value="">Loading Invoices...</option>';
        productSelect.disabled = true;
        productSelect.innerHTML = '<option value="">Select Invoice First...</option>';
        serialInput.value = '';

        fetch(`/warranty/customer/${customerId}/invoices-json`)
            .then(res => res.json())
            .then(data => {
                invoiceSelect.innerHTML = '<option value="">Select Purchased Invoice...</option>';
                if (data.length > 0) {
                    data.forEach(inv => {
                        const option = document.createElement('option');
                        option.value = inv.id;
                        option.textContent = `${inv.invoice_number} - Total: Rs. ${inv.total} (${inv.date})`;
                        invoiceSelect.appendChild(option);
                    });
                    invoiceSelect.disabled = false;
                } else {
                    invoiceSelect.innerHTML = '<option value="">No invoices found for this customer</option>';
                    invoiceSelect.disabled = true;
                }
            })
            .catch(err => {
                console.error("Error fetching customer invoices:", err);
                invoiceSelect.innerHTML = '<option value="">Error loading invoices</option>';
            });
    });

    // Handle Invoice Selection Change
    invoiceSelect.addEventListener('change', function() {
        const invoiceId = this.value;
        if (!invoiceId) {
            clearInvoiceDependentFields();
            return;
        }

        productSelect.disabled = true;
        productSelect.innerHTML = '<option value="">Loading Products...</option>';
        serialInput.value = '';

        // Fetch invoice items
        fetch(`/invoices/${invoiceId}/items-json`)
            .then(res => res.json())
            .then(data => {
                // Reset map
                invoiceItemsMap = {};

                productSelect.innerHTML = '<option value="">Select Catalog Item...</option>';
                data.items.forEach(item => {
                    invoiceItemsMap[item.product_id] = item.serial_number || "";
                    
                    const option = document.createElement('option');
                    option.value = item.product_id;
                    option.textContent = `${item.product_name} [SKU: ${item.sku}]`;
                    option.dataset.serial = item.serial_number || "";
                    productSelect.appendChild(option);
                });
                productSelect.disabled = false;
            })
            .catch(err => {
                console.error("Error fetching invoice items:", err);
                productSelect.innerHTML = '<option value="">Error loading products</option>';
            });
    });

    // Handle Product Selection Change
    productSelect.addEventListener('change', function() {
        const productId = this.value;
        if (productId && invoiceItemsMap[productId]) {
            serialInput.value = invoiceItemsMap[productId];
        } else {
            serialInput.value = "";
        }
    });

    function clearCustomerDependentFields() {
        invoiceSelect.innerHTML = '<option value="">Select Purchased Invoice...</option>';
        invoiceSelect.disabled = true;
        clearInvoiceDependentFields();
    }

    function clearInvoiceDependentFields() {
        productSelect.innerHTML = '<option value="">Select Catalog Item...</option>';
        productSelect.disabled = true;
        serialInput.value = "";
        invoiceItemsMap = {};
    }
});
</script>
@endsection
