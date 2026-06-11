@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">ADD NEW INVOICE</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold">Generate new billing & sales transactions</p>
        </div>
        <a href="{{ route('neuro_invoices.index') }}" class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 text-xs transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>


    <form action="{{ route('neuro_invoices.store') }}" method="POST" id="invoiceForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left 2 Columns: Customer, Metadata & Products -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- 1. Customer Details -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-user-gear text-cyan-400"></i>Enter Customer Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Select Customer</label>
                            <select name="customer_id" id="customerSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                                <option value="">Select customer (Or create new below)</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}" data-name="{{ $cust->name }}" data-phone="{{ $cust->phone }}" data-address="{{ $cust->address }}">{{ $cust->name }} ({{ $cust->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-center justify-center text-slate-500 text-xs font-bold uppercase tracking-wider md:pt-4">
                            <span>— OR —</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2" id="newCustomerFields">
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="customer_name" id="custNameInput" required placeholder="Enter name" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Mobile <span class="text-rose-500">*</span></label>
                            <input type="text" name="customer_mobile" id="custMobileInput" required placeholder="Enter mobile number" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Address</label>
                            <input type="text" name="customer_address" id="custAddressInput" placeholder="Enter address" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>
                </div>

                <!-- 2. Invoice Metadata -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice text-cyan-400"></i>Invoice Metadata
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Title Of Invoice</label>
                            <input type="text" name="title" placeholder="e.g. Custom Build System Sale" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Sale Type <span class="text-rose-500">*</span></label>
                            <select name="sale_type" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                                <option value="Shop">Shop</option>
                                <option value="Online">Online</option>
                                <option value="Corporate">Corporate</option>
                                <option value="Service">Service / Repair</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Special Note/User</label>
                            <textarea name="special_note" rows="1" placeholder="Add notes..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Select employee</label>
                            <select name="employee_id" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                                <option value="">Select employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->designation }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Due Date</label>
                            <input type="date" name="due_date" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                            <span class="text-[9px] text-slate-500 block mt-1">Leave blank if no due date</span>
                        </div>
                    </div>
                </div>

                <!-- Scanner Input Card -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-barcode text-cyan-400"></i>Barcode / QR / Serial Scanner
                    </h3>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                            <i class="fa-solid fa-camera text-xs animate-pulse text-cyan-400"></i>
                        </span>
                        <input type="text" id="scannerInput" placeholder="Scan Barcode, QR Code, or Serial Number..." class="w-full bg-slate-950 border border-slate-800 text-cyan-400 rounded-lg pl-9 pr-3 py-2 text-xs focus:outline-none focus:border-cyan-500 transition-colors uppercase tracking-widest font-bold mono-text" autofocus>
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-[9px] text-slate-500 font-bold uppercase tracking-wider select-none">
                            Ready for Input
                        </span>
                    </div>
                </div>

                <!-- 3. Add Products Selection -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-cart-plus text-cyan-400"></i>Add Products
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Product</label>
                            <select id="prodSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 select2">
                                <option value="">Select product</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}" data-sku="{{ $prod->sku }}" data-barcode="{{ $prod->barcode }}" data-price="{{ $prod->price }}" data-buying-price="{{ $prod->buying_price }}" data-stock="{{ $prod->stock }}" data-warranty="{{ $prod->warranty_months }}" data-serials="{{ json_encode($prod->serials->pluck('serial_number')) }}">{{ $prod->name }} [SKU: {{ $prod->sku }}] (Stock: {{ $prod->stock }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Cost Price (Rs.)</label>
                            <input type="number" id="prodCost" readonly class="w-full bg-slate-950 border border-slate-800 text-slate-400 rounded-lg px-3 py-2 text-xs focus:outline-none cursor-not-allowed">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Available Qty</label>
                            <input type="text" id="prodAvailableQty" readonly class="w-full bg-slate-950 border border-slate-800 text-slate-400 rounded-lg px-3 py-2 text-xs focus:outline-none cursor-not-allowed font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                        <div class="col-span-2">
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Sale Price (Rs.)</label>
                            <input type="number" id="prodSalePrice" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Quantity</label>
                            <input type="number" id="prodQty" value="1" min="1" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Free Qty</label>
                            <input type="number" id="prodFreeQty" value="0" min="0" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Discount (Rs.)</label>
                            <input type="number" id="prodDiscountFlat" value="0" min="0" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Discount %</label>
                            <input type="number" id="prodDiscountPercent" value="0" min="0" max="100" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4 pt-2 border-t border-slate-800/40">
                        <div class="text-xs">
                            <span class="text-slate-400">Total (Rs.):</span>
                            <span class="mono-text font-black text-cyan-400 ml-2" id="lblItemTotal">0.00</span>
                        </div>
                        <button type="button" id="btnAddProduct" class="px-5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs hover:bg-cyan-400 transition-colors uppercase tracking-widest flex items-center gap-1.5 shadow-neon-cyan">
                            <i class="fa-solid fa-plus-circle"></i>Add Item
                        </button>
                    </div>
                </div>

                <!-- 4. Selected Products Table -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                    <div class="p-5 border-b border-slate-800">
                        <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest">Selected Products</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-800 text-slate-400 uppercase font-semibold text-[10px] tracking-wider">
                                    <th class="py-3 px-4">Product</th>
                                    <th class="py-3 px-4 text-right">Sale Price</th>
                                    <th class="py-3 px-4 text-center">Qty</th>
                                    <th class="py-3 px-4 text-center">Free Qty</th>
                                    <th class="py-3 px-4 text-right">Discount Amt</th>
                                    <th class="py-3 px-4 text-center">Discount %</th>
                                    <th class="py-3 px-4 text-right">Total</th>
                                    <th class="py-3 px-4">Serial No</th>
                                    <th class="py-3 px-4">Warranty</th>
                                    <th class="py-3 px-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-850" id="selectedProductsTable">
                                <!-- Dynamic Items will render here -->
                                <tr id="noProductsRow">
                                    <td colspan="10" class="py-8 text-center text-slate-500 italic">No products added yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Totals, Calculations & Checkout -->
            <div class="space-y-6">
                
                <!-- 5. Checkout Calculations -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">
                        Summary & Calculations
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>Subtotal (Rs.):</span>
                            <span class="mono-text font-semibold text-slate-200" id="lblSubtotal">0.00</span>
                        </div>
                        
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Global Discount (%)</label>
                            <input type="number" name="global_discount_percentage" id="globalDiscountPercent" value="0" min="0" max="100" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                        </div>

                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Global Discount (Rs)</label>
                            <input type="number" name="global_discount_amount" id="globalDiscountFlat" value="0" min="0" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                        </div>

                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Service Charges</label>
                            <input type="number" name="service_charges" id="serviceCharges" value="0" min="0" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                        </div>

                        <div class="flex items-center gap-2 py-1">
                            <input type="checkbox" name="is_tax_invoice" id="taxCheckbox" value="1" class="rounded border-slate-800 bg-slate-950 text-cyan-500 focus:ring-cyan-500">
                            <label for="taxCheckbox" class="text-[10px] text-slate-300 font-bold uppercase tracking-wider select-none cursor-pointer">Apply 15% VAT Tax</label>
                        </div>

                        <div class="flex items-center justify-between border-t border-slate-800 pt-3 text-cyan-400">
                            <span class="text-xs font-bold uppercase tracking-wider">Grand Total (Rs.):</span>
                            <span class="mono-text text-lg font-black" id="lblGrandTotal">0.00</span>
                        </div>
                    </div>
                </div>

                <!-- 6. Payment Status and Action -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">
                        Payment & Checkout
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Payment Type <span class="text-rose-500">*</span></label>
                            <select name="payment_method" id="paymentMethodSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Koko">Koko (Installment)</option>
                                <option value="Payzy">Payzy (Installment)</option>
                            </select>
                        </div>

                        <div id="bankAccountContainer" style="display: none;">
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Select Bank Account *</label>
                            <select name="bank_account_id" id="bankAccountSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                                <option value="">Select Account...</option>
                                @foreach($bankAccounts as $ba)
                                    <option value="{{ $ba->id }}">{{ $ba->bank_name }} - {{ substr($ba->account_number, -4) }} ({{ $ba->account_name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Paid <span class="text-rose-500">*</span></label>
                            <select name="is_paid" id="isPaidSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Paid (Rs.)</label>
                            <input type="number" name="customer_paid" id="customerPaid" value="0" min="0" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                        </div>

                        <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                            <span>Balance (Rs.):</span>
                            <span class="mono-text font-bold" id="lblBalance">0.00</span>
                        </div>

                        <button type="submit" class="w-full py-3 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan flex items-center justify-center gap-2 mt-4">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>PROCESS SALES TRANSACTION</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select Elements
    const customerSelect = document.getElementById('customerSelect');
    const custNameInput = document.getElementById('custNameInput');
    const custMobileInput = document.getElementById('custMobileInput');
    const custAddressInput = document.getElementById('custAddressInput');

    const paymentMethodSelect = document.getElementById('paymentMethodSelect');
    const bankAccountContainer = document.getElementById('bankAccountContainer');
    const bankAccountSelect = document.getElementById('bankAccountSelect');

    paymentMethodSelect.addEventListener('change', function() {
        if (this.value === 'Bank Transfer') {
            bankAccountContainer.style.display = 'block';
            bankAccountSelect.required = true;
        } else {
            bankAccountContainer.style.display = 'none';
            bankAccountSelect.required = false;
            bankAccountSelect.value = '';
        }
    });

    const prodSelect = document.getElementById('prodSelect');
    const prodCost = document.getElementById('prodCost');
    const prodSalePrice = document.getElementById('prodSalePrice');
    const prodQty = document.getElementById('prodQty');
    const prodFreeQty = document.getElementById('prodFreeQty');
    const prodDiscountFlat = document.getElementById('prodDiscountFlat');
    const prodDiscountPercent = document.getElementById('prodDiscountPercent');
    const lblItemTotal = document.getElementById('lblItemTotal');
    const btnAddProduct = document.getElementById('btnAddProduct');

    const selectedProductsTable = document.getElementById('selectedProductsTable');
    const noProductsRow = document.getElementById('noProductsRow');

    const globalDiscountPercent = document.getElementById('globalDiscountPercent');
    const globalDiscountFlat = document.getElementById('globalDiscountFlat');
    const serviceCharges = document.getElementById('serviceCharges');
    const taxCheckbox = document.getElementById('taxCheckbox');
    const customerPaid = document.getElementById('customerPaid');
    const isPaidSelect = document.getElementById('isPaidSelect');

    const lblSubtotal = document.getElementById('lblSubtotal');
    const lblGrandTotal = document.getElementById('lblGrandTotal');
    const lblBalance = document.getElementById('lblBalance');

    let cart = [];

    // Toggle customer details if selected
    customerSelect.addEventListener('change', function() {
        const option = customerSelect.options[customerSelect.selectedIndex];
        if (option.value) {
            custNameInput.value = option.dataset.name || '';
            custMobileInput.value = option.dataset.phone || '';
            custAddressInput.value = option.dataset.address || '';
            custNameInput.readOnly = true;
            custMobileInput.readOnly = true;
            custAddressInput.readOnly = true;
            custNameInput.classList.add('text-slate-400');
            custMobileInput.classList.add('text-slate-400');
            custAddressInput.classList.add('text-slate-400');
            custNameInput.required = false;
            custMobileInput.required = false;
        } else {
            custNameInput.value = '';
            custMobileInput.value = '';
            custAddressInput.value = '';
            custNameInput.readOnly = false;
            custMobileInput.readOnly = false;
            custAddressInput.readOnly = false;
            custNameInput.classList.remove('text-slate-400');
            custMobileInput.classList.remove('text-slate-400');
            custAddressInput.classList.remove('text-slate-400');
            custNameInput.required = true;
            custMobileInput.required = true;
        }
    });

    // When product selection changes
    prodSelect.addEventListener('change', function() {
        const option = prodSelect.options[prodSelect.selectedIndex];
        if (option.value) {
            prodCost.value = parseFloat(option.dataset.buyingPrice).toFixed(2);
            document.getElementById('prodAvailableQty').value = option.dataset.stock || '0';
            prodSalePrice.value = parseFloat(option.dataset.price).toFixed(2);
            prodQty.value = 1;
            prodFreeQty.value = 0;
            prodDiscountFlat.value = 0;
            prodDiscountPercent.value = 0;
            calculateItemTotal();
        } else {
            prodCost.value = '';
            document.getElementById('prodAvailableQty').value = '';
            prodSalePrice.value = '';
            lblItemTotal.textContent = '0.00';
        }
    });

    // Calculate item total in the "Add Products" interface
    function calculateItemTotal() {
        const salePrice = parseFloat(prodSalePrice.value) || 0;
        const qty = parseInt(prodQty.value) || 0;
        const discFlat = parseFloat(prodDiscountFlat.value) || 0;

        const sub = salePrice * qty;
        const total = Math.max(0, sub - discFlat);
        lblItemTotal.textContent = total.toFixed(2);
    }

    // Link item discount inputs
    prodDiscountFlat.addEventListener('input', function() {
        const salePrice = parseFloat(prodSalePrice.value) || 0;
        const qty = parseInt(prodQty.value) || 0;
        const flatVal = parseFloat(prodDiscountFlat.value) || 0;
        const base = salePrice * qty;
        
        if (base > 0) {
            const pct = (flatVal / base) * 100;
            prodDiscountPercent.value = pct.toFixed(2);
        } else {
            prodDiscountPercent.value = 0;
        }
        calculateItemTotal();
    });

    prodDiscountPercent.addEventListener('input', function() {
        const salePrice = parseFloat(prodSalePrice.value) || 0;
        const qty = parseInt(prodQty.value) || 0;
        const pctVal = parseFloat(prodDiscountPercent.value) || 0;
        const base = salePrice * qty;
        
        const flat = base * (pctVal / 100);
        prodDiscountFlat.value = flat.toFixed(2);
        calculateItemTotal();
    });

    prodSalePrice.addEventListener('input', calculateItemTotal);
    prodQty.addEventListener('input', calculateItemTotal);

    // Add product to cart
    btnAddProduct.addEventListener('click', function() {
        const option = prodSelect.options[prodSelect.selectedIndex];
        if (!option.value) {
            alert('Please select a product.');
            return;
        }

        const id = parseInt(option.value);
        const name = option.text.split('[SKU')[0].trim();
        const price = parseFloat(prodSalePrice.value) || 0;
        const qty = parseInt(prodQty.value) || 0;
        const freeQty = parseInt(prodFreeQty.value) || 0;
        const discFlat = parseFloat(prodDiscountFlat.value) || 0;
        const discPct = parseFloat(prodDiscountPercent.value) || 0;
        const warranty = option.dataset.warranty ? option.dataset.warranty + ' months' : 'No warranty';
        const serials = JSON.parse(option.dataset.serials || '[]');

        if (qty <= 0) {
            alert('Quantity must be at least 1.');
            return;
        }

        const itemTotal = Math.max(0, (price * qty) - discFlat);

        // Add to array
        cart.push({
            id: id,
            name: name,
            price: price,
            qty: qty,
            freeQty: freeQty,
            discountFlat: discFlat,
            discountPct: discPct,
            total: itemTotal,
            warranty: warranty,
            serials: serials,
            selectedSerial: ''
        });

        // Reset inputs
        prodSelect.value = '';
        prodCost.value = '';
        document.getElementById('prodAvailableQty').value = '';
        prodSalePrice.value = '';
        prodQty.value = 1;
        prodFreeQty.value = 0;
        prodDiscountFlat.value = 0;
        prodDiscountPercent.value = 0;
        lblItemTotal.textContent = '0.00';

        renderCartTable();
    });

    // Render selected items
    function renderCartTable() {
        const items = selectedProductsTable.getElementsByClassName('cart-row');
        while(items.length > 0) items[0].remove();

        if (cart.length === 0) {
            noProductsRow.style.display = 'table-row';
            calculateCheckoutTotals();
            return;
        }

        noProductsRow.style.display = 'none';

        cart.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.className = 'cart-row hover:bg-slate-800/10 transition-colors';

            // Serial selector html
            let serialHtml = '<span class="text-slate-500 text-[10px]">No serials available</span>';
            if (item.serials && item.serials.length > 0) {
                serialHtml = `<select name="items[${index}][serial_number]" class="bg-slate-950 border border-slate-800 text-slate-200 rounded px-2 py-1 text-[10px] focus:outline-none focus:border-cyan-500 w-32">
                    <option value="">Select Serial</option>
                    ${item.serials.map(s => `<option value="${s}" ${item.selectedSerial === s ? 'selected' : ''}>${s}</option>`).join('')}
                </select>`;
            }

            tr.innerHTML = `
                <td class="py-3 px-4">
                    <span class="font-bold text-slate-200 block">${item.name}</span>
                    <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                </td>
                <td class="py-3 px-4 text-right mono-text">
                    <input type="number" name="items[${index}][unit_price]" value="${item.price.toFixed(2)}" step="0.01" class="bg-slate-950 border border-slate-800 text-slate-200 text-right rounded px-2 py-1 text-[10px] focus:outline-none focus:border-cyan-500 w-20 row-price">
                </td>
                <td class="py-3 px-4 text-center">
                    <input type="number" name="items[${index}][quantity]" value="${item.qty}" min="1" class="bg-slate-950 border border-slate-800 text-slate-200 text-center rounded px-2 py-1 text-[10px] focus:outline-none focus:border-cyan-500 w-12 row-qty">
                </td>
                <td class="py-3 px-4 text-center">
                    <input type="number" name="items[${index}][free_quantity]" value="${item.freeQty}" min="0" class="bg-slate-950 border border-slate-800 text-slate-200 text-center rounded px-2 py-1 text-[10px] focus:outline-none focus:border-cyan-500 w-12 row-free-qty">
                </td>
                <td class="py-3 px-4 text-right mono-text">
                    <input type="number" name="items[${index}][discount_amount]" value="${item.discountFlat.toFixed(2)}" step="0.01" class="bg-slate-950 border border-slate-800 text-slate-200 text-right rounded px-2 py-1 text-[10px] focus:outline-none focus:border-cyan-500 w-20 row-disc-flat">
                </td>
                <td class="py-3 px-4 text-center mono-text">
                    <input type="number" name="items[${index}][discount_percentage]" value="${item.discountPct.toFixed(2)}" step="0.01" class="bg-slate-950 border border-slate-800 text-slate-200 text-center rounded px-2 py-1 text-[10px] focus:outline-none focus:border-cyan-500 w-16 row-disc-pct">
                </td>
                <td class="py-3 px-4 text-right font-bold text-slate-200 mono-text row-total">${item.total.toFixed(2)}</td>
                <td class="py-3 px-4">${serialHtml}</td>
                <td class="py-3 px-4">
                    <input type="text" name="items[${index}][warranty]" value="${item.warranty}" class="bg-slate-950 border border-slate-800 text-slate-200 rounded px-2 py-1 text-[10px] focus:outline-none focus:border-cyan-500 w-24">
                </td>
                <td class="py-3 px-4 text-center">
                    <button type="button" class="text-rose-500 hover:text-rose-400 text-xs font-bold btn-delete"><i class="fa-solid fa-trash"></i></button>
                </td>
            `;

            // Setup listeners inside the row for dynamic changes
            const rowPrice = tr.querySelector('.row-price');
            const rowQty = tr.querySelector('.row-qty');
            const rowFreeQty = tr.querySelector('.row-free-qty');
            const rowDiscFlat = tr.querySelector('.row-disc-flat');
            const rowDiscPct = tr.querySelector('.row-disc-pct');
            const rowTotal = tr.querySelector('.row-total');
            const btnDelete = tr.querySelector('.btn-delete');
            const serialSelect = tr.querySelector('select');

            function updateRowTotal() {
                const prc = parseFloat(rowPrice.value) || 0;
                const qt = parseInt(rowQty.value) || 0;
                const flat = parseFloat(rowDiscFlat.value) || 0;
                
                const tot = Math.max(0, (prc * qt) - flat);
                rowTotal.textContent = tot.toFixed(2);
                
                item.price = prc;
                item.qty = qt;
                item.freeQty = parseInt(rowFreeQty.value) || 0;
                item.discountFlat = flat;
                item.discountPct = parseFloat(rowDiscPct.value) || 0;
                item.total = tot;

                calculateCheckoutTotals();
            }

            rowPrice.addEventListener('input', updateRowTotal);
            rowQty.addEventListener('input', updateRowTotal);
            rowFreeQty.addEventListener('input', updateRowTotal);

            rowDiscFlat.addEventListener('input', function() {
                const prc = parseFloat(rowPrice.value) || 0;
                const qt = parseInt(rowQty.value) || 0;
                const flat = parseFloat(rowDiscFlat.value) || 0;
                const base = prc * qt;
                if (base > 0) {
                    rowDiscPct.value = ((flat / base) * 100).toFixed(2);
                } else {
                    rowDiscPct.value = 0;
                }
                updateRowTotal();
            });

            rowDiscPct.addEventListener('input', function() {
                const prc = parseFloat(rowPrice.value) || 0;
                const qt = parseInt(rowQty.value) || 0;
                const pct = parseFloat(rowDiscPct.value) || 0;
                const base = prc * qt;
                rowDiscFlat.value = (base * (pct / 100)).toFixed(2);
                updateRowTotal();
            });

            if (serialSelect) {
                serialSelect.addEventListener('change', function() {
                    item.selectedSerial = serialSelect.value;
                });
            }

            btnDelete.addEventListener('click', function() {
                cart.splice(index, 1);
                renderCartTable();
            });

            selectedProductsTable.appendChild(tr);
        });

        calculateCheckoutTotals();
    }

    // Calculate checkout totals
    function calculateCheckoutTotals() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += item.total;
        });

        lblSubtotal.textContent = subtotal.toFixed(2);

        // Global discount logic
        const gDiscountPercent = parseFloat(globalDiscountPercent.value) || 0;
        let gDiscountFlat = parseFloat(globalDiscountFlat.value) || 0;
        const charges = parseFloat(serviceCharges.value) || 0;

        const taxRate = taxCheckbox.checked ? 0.15 : 0;
        const taxableAmount = Math.max(0, subtotal - gDiscountFlat);
        const tax = taxableAmount * taxRate;
        const grandTotal = taxableAmount + tax + charges;

        lblGrandTotal.textContent = grandTotal.toFixed(2);

        // Balance calculation
        const paid = parseFloat(customerPaid.value) || 0;
        const balance = paid - grandTotal;
        lblBalance.textContent = balance.toFixed(2);
    }

    // Link global discount inputs
    globalDiscountPercent.addEventListener('input', function() {
        let subtotal = 0;
        cart.forEach(item => { subtotal += item.total; });
        const pct = parseFloat(globalDiscountPercent.value) || 0;
        globalDiscountFlat.value = (subtotal * (pct / 100)).toFixed(2);
        calculateCheckoutTotals();
    });

    globalDiscountFlat.addEventListener('input', function() {
        let subtotal = 0;
        cart.forEach(item => { subtotal += item.total; });
        const flat = parseFloat(globalDiscountFlat.value) || 0;
        if (subtotal > 0) {
            globalDiscountPercent.value = ((flat / subtotal) * 100).toFixed(2);
        } else {
            globalDiscountPercent.value = 0;
        }
        calculateCheckoutTotals();
    });

    serviceCharges.addEventListener('input', calculateCheckoutTotals);
    taxCheckbox.addEventListener('change', calculateCheckoutTotals);
    customerPaid.addEventListener('input', calculateCheckoutTotals);

    // Form submission validation
    document.getElementById('invoiceForm').addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            showDynamicToast('Please add at least one product to the invoice.', 'error');
            return;
        }

        // Validate serials if any product has serial options
        for (let i = 0; i < cart.length; i++) {
            const item = cart[i];
            if (item.serials && item.serials.length > 0 && !item.selectedSerial) {
                e.preventDefault();
                showDynamicToast(`Please select a serial number for ${item.name}.`, 'error');
                return;
            }
        }
    });

    // Scanner Input Keyboard Listener
    const scannerInput = document.getElementById('scannerInput');
    if (scannerInput) {
        scannerInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const code = this.value.trim();
                if (code) {
                    handleScannedCode(code);
                }
                this.value = '';
            }
        });
    }

    // Process scanned code (serial, SKU, or Barcode)
    function handleScannedCode(code) {
        const options = Array.from(prodSelect.options);
        let serialMatchedOption = null;
        let matchedSerial = '';

        // 1. Search for unique serial match (case-insensitive check against each product's available serials)
        for (let opt of options) {
            if (!opt.value) continue;
            const serials = JSON.parse(opt.dataset.serials || '[]');
            const found = serials.find(s => s.toUpperCase() === code.toUpperCase());
            if (found) {
                serialMatchedOption = opt;
                matchedSerial = found; // exact case from database
                break;
            }
        }

        if (serialMatchedOption) {
            // Check if this serial is already added to cart
            const isDuplicate = cart.some(item => item.selectedSerial.toUpperCase() === matchedSerial.toUpperCase());
            if (isDuplicate) {
                showDynamicToast(`Serial number "${matchedSerial}" is already added to this invoice!`, 'error');
                return;
            }

            const id = parseInt(serialMatchedOption.value);
            const name = serialMatchedOption.text.split('[SKU')[0].trim();
            const price = parseFloat(serialMatchedOption.dataset.price) || 0;
            const warranty = serialMatchedOption.dataset.warranty ? serialMatchedOption.dataset.warranty + ' months' : 'No warranty';
            const serials = JSON.parse(serialMatchedOption.dataset.serials || '[]');

            cart.push({
                id: id,
                name: name,
                price: price,
                qty: 1,
                freeQty: 0,
                discountFlat: 0,
                discountPct: 0,
                total: price,
                warranty: warranty,
                serials: serials,
                selectedSerial: matchedSerial
            });

            renderCartTable();
            showDynamicToast(`Serial "${matchedSerial}" (${name}) added successfully.`, 'success');
            return;
        }

        // 2. Search for Product SKU or Barcode match
        let productMatchedOption = null;
        for (let opt of options) {
            if (!opt.value) continue;
            const sku = (opt.dataset.sku || '').toUpperCase();
            const barcode = (opt.dataset.barcode || '').toUpperCase();
            const upperCode = code.toUpperCase();
            if (sku === upperCode || barcode === upperCode) {
                productMatchedOption = opt;
                break;
            }
        }

        if (productMatchedOption) {
            const id = parseInt(productMatchedOption.value);
            const name = productMatchedOption.text.split('[SKU')[0].trim();
            const price = parseFloat(productMatchedOption.dataset.price) || 0;
            const warranty = productMatchedOption.dataset.warranty ? productMatchedOption.dataset.warranty + ' months' : 'No warranty';
            const serials = JSON.parse(productMatchedOption.dataset.serials || '[]');
            const isSerialized = serials.length > 0;

            if (isSerialized) {
                // Serialized products must be added as separate rows with quantity 1
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    qty: 1,
                    freeQty: 0,
                    discountFlat: 0,
                    discountPct: 0,
                    total: price,
                    warranty: warranty,
                    serials: serials,
                    selectedSerial: ''
                });
                renderCartTable();
                showDynamicToast(`Serialized Product "${name}" added. Please select its Serial Number.`, 'success');
            } else {
                // Non-serialized products can increment quantity on the same row
                const existingItem = cart.find(item => item.id === id);
                if (existingItem) {
                    existingItem.qty += 1;
                    existingItem.total = Math.max(0, (existingItem.price * existingItem.qty) - existingItem.discountFlat);
                    renderCartTable();
                    showDynamicToast(`Quantity for "${name}" incremented to ${existingItem.qty}.`, 'success');
                } else {
                    cart.push({
                        id: id,
                        name: name,
                        price: price,
                        qty: 1,
                        freeQty: 0,
                        discountFlat: 0,
                        discountPct: 0,
                        total: price,
                        warranty: warranty,
                        serials: serials,
                        selectedSerial: ''
                    });
                    renderCartTable();
                    showDynamicToast(`Product "${name}" added successfully.`, 'success');
                }
            }
            return;
        }

        // 3. Not found
        showDynamicToast(`Scanned code "${code}" not found in system or out of stock!`, 'error');
    }

    // Dynamic toast notification generator
    function showDynamicToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast-item toast-${type}`;
        
        const iconClass = type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation';
        
        toast.innerHTML = `
            <div class="flex items-center gap-2.5">
                <i class="fa-solid ${iconClass} text-sm"></i>
                <span>${message}</span>
            </div>
            <button class="toast-close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.add('toast-show');
        }, 10);
        
        // Auto close
        const autoClose = setTimeout(() => {
            dismissToast(toast);
        }, 5000);
        
        // Close button functionality
        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                clearTimeout(autoClose);
                dismissToast(toast);
            });
        }
        
        function dismissToast(el) {
            el.classList.remove('toast-show');
            setTimeout(() => {
                el.remove();
            }, 300);
        }
    }
});
</script>
@endsection
