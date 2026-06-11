@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <h1 class="orbitron-title text-xl font-black text-slate-100 tracking-wider">CREATING GRN ORDER</h1>
            <p class="text-slate-400 text-xs mt-0.5 uppercase tracking-widest font-semibold">Log incoming shipments, set wholesale and retail prices, and update stock</p>
        </div>
        <a href="{{ route('grn.index') }}" class="px-4 py-2 bg-slate-900 border border-slate-800 rounded-lg text-slate-400 hover:text-slate-200 text-xs transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>


    <form action="{{ route('grn.store') }}" method="POST" id="grnForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left 2 Columns: Supplier, Metadata & Products -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- 1. Supplier & Basic Info -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-truck-ramp-box text-cyan-400"></i>Shipment Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Select Supplier <span class="text-rose-500">*</span></label>
                            <select name="supplier_id" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->company_name }} ({{ $supplier->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Received Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="date_received" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>

                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">GRN NO</label>
                            <input type="text" name="grn_number" placeholder="Auto-generated if blank" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Notes / References</label>
                        <textarea name="notes" rows="1" placeholder="e.g. Invoice #SUP-2991, custom shipping notes..." class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 resize-none"></textarea>
                    </div>
                </div>

                <!-- 2. Add Products Selection -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-cart-plus text-cyan-400"></i>Add Products
                    </h3>

                    <!-- Product Select & Prices Row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Product</label>
                            <select id="prodSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                                <option value="">Select product</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->name }} [SKU: {{ $prod->sku }}] (Stock: {{ $prod->stock }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Available Stock</label>
                            <input type="text" id="prodAvailableStock" readonly value="0" class="w-full bg-slate-950 border border-slate-800 text-slate-400 rounded-lg px-3 py-2 text-xs focus:outline-none cursor-not-allowed font-mono">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Barcode</label>
                            <input type="text" id="prodBarcode" placeholder="Scan or enter barcode" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>

                    <!-- Cost & Selling Prices -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Cost Price (Rs.)</label>
                            <input type="number" id="prodCostPrice" step="0.01" min="0" value="0.00" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 font-mono">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Sale Price (Rs.)</label>
                            <input type="number" id="prodSalePrice" step="0.01" min="0" value="0.00" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 font-mono">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Whole Sale Price (Rs.)</label>
                            <input type="number" id="prodWholeSalePrice" step="0.01" min="0" value="0.00" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 font-mono">
                        </div>
                    </div>

                    <!-- Quantities & Expiry -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Accept Quantity</label>
                            <input type="number" id="prodAcceptQty" value="1" min="1" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Free Quantity</label>
                            <input type="number" id="prodFreeQty" value="0" min="0" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Warranty (months)</label>
                            <input type="number" id="prodWarranty" value="12" min="0" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Expire Date</label>
                            <input type="date" id="prodExpireDate" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>

                    <!-- Discount Calculations -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Discount % (for cost price)</label>
                            <input type="number" id="prodDiscountPercent" value="0" min="0" max="100" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Single Discount amount for item</label>
                            <input type="number" id="prodSingleDiscountAmount" value="0" min="0" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Discount amount for item</label>
                            <input type="number" id="prodDiscountAmount" value="0" min="0" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>

                    <!-- Note Alert & Add Row -->
                    <div class="bg-cyan-500/5 border border-cyan-500/20 text-cyan-400/90 text-[10px] p-3 rounded-lg flex items-start gap-2.5 leading-relaxed">
                        <i class="fa-solid fa-circle-info mt-0.5 text-xs text-cyan-400"></i>
                        <span><strong>Note:-</strong> If you enter any number for discount % it will replace the item cost price and it will calculate that discount from sale price as the cost price.</span>
                    </div>

                    <div class="flex items-center justify-between gap-4 pt-2 border-t border-slate-800/40">
                        <div class="grid grid-cols-2 gap-6 text-xs">
                            <div>
                                <span class="text-slate-400">Total Cost (Rs.):</span>
                                <span class="mono-text font-black text-cyan-400 ml-2" id="lblItemTotalCost">0.00</span>
                            </div>
                            <div>
                                <span class="text-slate-400">Total Sale (Rs.):</span>
                                <span class="mono-text font-black text-emerald-400 ml-2" id="lblItemTotalSale">0.00</span>
                            </div>
                        </div>
                        <button type="button" id="btnAddProduct" class="px-5 py-2 bg-cyan-500 text-slate-950 font-bold rounded-lg text-xs hover:bg-cyan-400 transition-colors uppercase tracking-widest flex items-center gap-1.5 shadow-neon-cyan">
                            <i class="fa-solid fa-plus-circle"></i>Add Item
                        </button>
                    </div>
                </div>

                <!-- 3. Selected Products Table -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                    <div class="p-5 border-b border-slate-800">
                        <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest">Selected Products</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-800 text-slate-400 uppercase font-semibold text-[10px] tracking-wider">
                                    <th class="py-3 px-4">Product</th>
                                    <th class="py-3 px-4 text-right">Cost Price</th>
                                    <th class="py-3 px-4 text-right">Sale Price</th>
                                    <th class="py-3 px-4">Barcode</th>
                                    <th class="py-3 px-4 text-center">Received Quantity</th>
                                    <th class="py-3 px-4 text-right">Total</th>
                                    <th class="py-3 px-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-850" id="selectedProductsTable">
                                <tr id="noProductsRow">
                                    <td colspan="7" class="py-8 text-center text-slate-500 italic">No products added yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Totals & Calculations -->
            <div class="space-y-6">
                
                <!-- 4. Calculations Summary -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">
                        Summary & Calculations
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>Subtotal (Rs.):</span>
                            <span class="mono-text font-semibold text-slate-200" id="lblSubtotal">0.00</span>
                            <input type="hidden" name="subtotal" id="subtotalInput" value="0.00">
                        </div>
                        
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Final Discount (%)</label>
                            <input type="number" name="discount_percentage" id="finalDiscountPercent" value="0" min="0" max="100" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                        </div>

                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Final Discount (Rs)</label>
                            <input type="number" name="discount_amount" id="finalDiscountFlat" value="0" min="0" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                        </div>

                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Service/Courier/Transport Charges</label>
                            <input type="number" name="service_charges" id="serviceCharges" value="0" min="0" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                        </div>

                        <div class="flex items-center justify-between border-t border-slate-800 pt-3 text-cyan-400">
                            <span class="text-xs font-bold uppercase tracking-wider">Grand Total (Rs.):</span>
                            <span class="mono-text text-lg font-black" id="lblGrandTotal">0.00</span>
                            <input type="hidden" name="total_amount" id="grandTotalInput" value="0.00">
                        </div>
                    </div>
                </div>

                <!-- 5. Payment & Checkout -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 space-y-4">
                    <h3 class="orbitron-title text-xs font-black text-cyan-400 uppercase tracking-widest border-b border-slate-800 pb-2">
                        Payment & Status
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Payment Type <span class="text-rose-500">*</span></label>
                            <select name="payment_type" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Koko">Koko</option>
                                <option value="Payzy">Payzy</option>
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
                            <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Paid Amount (Rs.) <span class="text-rose-500">*</span></label>
                            <input type="number" name="paid_amount" id="paidAmount" value="0.00" min="0" step="0.01" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 font-mono">
                        </div>

                        <button type="submit" class="w-full py-3 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan flex items-center justify-center gap-2 mt-4">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span>PROCESS GRN ORDER</span>
                        </button>
                    </div>
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
    const products = JSON.parse(document.getElementById('products-data').textContent);
    
    // Elements
    const prodSelect = document.getElementById('prodSelect');
    const prodAvailableStock = document.getElementById('prodAvailableStock');
    const prodBarcode = document.getElementById('prodBarcode');
    const prodCostPrice = document.getElementById('prodCostPrice');
    const prodSalePrice = document.getElementById('prodSalePrice');
    const prodWholeSalePrice = document.getElementById('prodWholeSalePrice');
    const prodAcceptQty = document.getElementById('prodAcceptQty');
    const prodFreeQty = document.getElementById('prodFreeQty');
    const prodWarranty = document.getElementById('prodWarranty');
    const prodExpireDate = document.getElementById('prodExpireDate');
    const prodDiscountPercent = document.getElementById('prodDiscountPercent');
    const prodSingleDiscountAmount = document.getElementById('prodSingleDiscountAmount');
    const prodDiscountAmount = document.getElementById('prodDiscountAmount');
    const lblItemTotalCost = document.getElementById('lblItemTotalCost');
    const lblItemTotalSale = document.getElementById('lblItemTotalSale');
    const btnAddProduct = document.getElementById('btnAddProduct');

    const selectedProductsTable = document.getElementById('selectedProductsTable');
    const noProductsRow = document.getElementById('noProductsRow');

    const lblSubtotal = document.getElementById('lblSubtotal');
    const finalDiscountPercent = document.getElementById('finalDiscountPercent');
    const finalDiscountFlat = document.getElementById('finalDiscountFlat');
    const serviceCharges = document.getElementById('serviceCharges');
    const lblGrandTotal = document.getElementById('lblGrandTotal');
    const grandTotalInput = document.getElementById('grandTotalInput');
    const isPaidSelect = document.getElementById('isPaidSelect');
    const paidAmount = document.getElementById('paidAmount');

    let cart = [];

    // Trigger Calculations on select change
    prodSelect.addEventListener('change', function() {
        const id = this.value;
        if (!id) {
            resetAddInputs();
            return;
        }

        const product = products.find(p => p.id == id);
        if (product) {
            prodAvailableStock.value = product.stock || '0';
            prodBarcode.value = product.barcode || '';
            prodCostPrice.value = parseFloat(product.buying_price || 0).toFixed(2);
            prodSalePrice.value = parseFloat(product.price || 0).toFixed(2);
            prodWholeSalePrice.value = parseFloat(product.wholesale_price || 0).toFixed(2);
            prodWarranty.value = product.warranty_months !== undefined ? product.warranty_months : 12;
            prodExpireDate.value = product.expire_date ? product.expire_date.substring(0, 10) : '';
            
            prodAcceptQty.value = 1;
            prodFreeQty.value = 0;
            prodDiscountPercent.value = 0;
            prodSingleDiscountAmount.value = 0;
            prodDiscountAmount.value = 0;
            
            calculateItemTotals();
        }
    });

    // Reset Inputs helper
    function resetAddInputs() {
        prodSelect.value = '';
        prodAvailableStock.value = '0';
        prodBarcode.value = '';
        prodCostPrice.value = '0.00';
        prodSalePrice.value = '0.00';
        prodWholeSalePrice.value = '0.00';
        prodAcceptQty.value = 1;
        prodFreeQty.value = 0;
        prodWarranty.value = 12;
        prodExpireDate.value = '';
        prodDiscountPercent.value = 0;
        prodSingleDiscountAmount.value = 0;
        prodDiscountAmount.value = 0;
        lblItemTotalCost.textContent = '0.00';
        lblItemTotalSale.textContent = '0.00';
    }

    // Dynamic Row Calculations
    function calculateItemTotals() {
        const costPrice = parseFloat(prodCostPrice.value) || 0;
        const salePrice = parseFloat(prodSalePrice.value) || 0;
        const qty = parseInt(prodAcceptQty.value) || 0;
        const singleDisc = parseFloat(prodSingleDiscountAmount.value) || 0;

        const totalCost = Math.max(0, (costPrice - singleDisc) * qty);
        const totalSale = salePrice * qty;

        lblItemTotalCost.textContent = totalCost.toFixed(2);
        lblItemTotalSale.textContent = totalSale.toFixed(2);
    }

    // Cost discount % replaces cost price and calculates it based on retail price
    prodDiscountPercent.addEventListener('input', function() {
        const pct = parseFloat(this.value) || 0;
        const salePrice = parseFloat(prodSalePrice.value) || 0;
        if (pct > 0 && salePrice > 0) {
            const calculatedCost = salePrice * (1 - (pct / 100));
            prodCostPrice.value = calculatedCost.toFixed(2);
        }
        calculateItemTotals();
    });

    // Single discount vs flat discount item links
    prodSingleDiscountAmount.addEventListener('input', function() {
        const single = parseFloat(this.value) || 0;
        const qty = parseInt(prodAcceptQty.value) || 0;
        prodDiscountAmount.value = (single * qty).toFixed(2);
        calculateItemTotals();
    });

    prodDiscountAmount.addEventListener('input', function() {
        const flat = parseFloat(this.value) || 0;
        const qty = parseInt(prodAcceptQty.value) || 0;
        if (qty > 0) {
            prodSingleDiscountAmount.value = (flat / qty).toFixed(2);
        } else {
            prodSingleDiscountAmount.value = 0;
        }
        calculateItemTotals();
    });

    prodCostPrice.addEventListener('input', function() {
        // If cost price is manually set, calculate corresponding discount % if salePrice is set
        const cost = parseFloat(this.value) || 0;
        const sale = parseFloat(prodSalePrice.value) || 0;
        if (sale > 0 && cost <= sale) {
            prodDiscountPercent.value = (((sale - cost) / sale) * 100).toFixed(2);
        } else {
            prodDiscountPercent.value = 0;
        }
        calculateItemTotals();
    });

    prodSalePrice.addEventListener('input', calculateItemTotals);
    prodAcceptQty.addEventListener('input', function() {
        const qty = parseInt(this.value) || 0;
        const single = parseFloat(prodSingleDiscountAmount.value) || 0;
        prodDiscountAmount.value = (single * qty).toFixed(2);
        calculateItemTotals();
    });

    // Add to cart array
    btnAddProduct.addEventListener('click', function() {
        const id = prodSelect.value;
        if (!id) {
            alert('Please select a product.');
            return;
        }

        const product = products.find(p => p.id == id);
        const costPrice = parseFloat(prodCostPrice.value) || 0;
        const salePrice = parseFloat(prodSalePrice.value) || 0;
        const wholeSalePrice = parseFloat(prodWholeSalePrice.value) || 0;
        const barcode = prodBarcode.value.trim();
        const qty = parseInt(prodAcceptQty.value) || 0;
        const freeQty = parseInt(prodFreeQty.value) || 0;
        const warranty = parseInt(prodWarranty.value) || 0;
        const expireDate = prodExpireDate.value;
        const discPercent = parseFloat(prodDiscountPercent.value) || 0;
        const singleDisc = parseFloat(prodSingleDiscountAmount.value) || 0;
        const discAmount = parseFloat(prodDiscountAmount.value) || 0;

        if (qty <= 0) {
            alert('Accept Quantity must be at least 1.');
            return;
        }

        const itemTotal = Math.max(0, (costPrice - singleDisc) * qty);

        // Add
        cart.push({
            product_id: id,
            name: product.name,
            sku: product.sku,
            buying_price: costPrice,
            price: salePrice,
            wholesale_price: wholeSalePrice,
            barcode: barcode,
            quantity: qty,
            free_quantity: freeQty,
            discount_percentage: discPercent,
            discount_amount: discAmount,
            single_discount_amount: singleDisc,
            warranty_months: warranty,
            expire_date: expireDate,
            total: itemTotal
        });

        resetAddInputs();
        renderCartTable();
    });

    function renderCartTable() {
        const rows = selectedProductsTable.getElementsByClassName('cart-row');
        while(rows.length > 0) rows[0].remove();

        if (cart.length === 0) {
            noProductsRow.style.display = 'table-row';
            calculateCheckoutTotals();
            return;
        }

        noProductsRow.style.display = 'none';

        cart.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.className = 'cart-row hover:bg-slate-800/10 transition-colors';

            const qtyText = item.free_quantity > 0 ? `${item.quantity} (+ ${item.free_quantity} Free)` : `${item.quantity}`;

            tr.innerHTML = `
                <td class="py-3 px-4">
                    <span class="font-bold text-slate-200 block">${item.name}</span>
                    <span class="text-[10px] text-slate-500">SKU: ${item.sku}</span>
                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                    <input type="hidden" name="items[${index}][wholesale_price]" value="${item.wholesale_price}">
                    <input type="hidden" name="items[${index}][barcode]" value="${item.barcode}">
                    <input type="hidden" name="items[${index}][expire_date]" value="${item.expire_date}">
                    <input type="hidden" name="items[${index}][free_quantity]" value="${item.free_quantity}">
                    <input type="hidden" name="items[${index}][discount_percentage]" value="${item.discount_percentage}">
                    <input type="hidden" name="items[${index}][discount_amount]" value="${item.discount_amount}">
                    <input type="hidden" name="items[${index}][single_discount_amount]" value="${item.single_discount_amount}">
                    <input type="hidden" name="items[${index}][warranty_months]" value="${item.warranty_months}">
                </td>
                <td class="py-3 px-4 text-right mono-text">
                    <input type="number" name="items[${index}][buying_price]" value="${item.buying_price.toFixed(2)}" step="0.01" class="bg-slate-950 border border-slate-800 text-slate-200 text-right rounded px-2 py-1 text-[10px] focus:outline-none focus:border-cyan-500 w-20 row-cost">
                </td>
                <td class="py-3 px-4 text-right mono-text">
                    <input type="number" name="items[${index}][price]" value="${item.price.toFixed(2)}" step="0.01" class="bg-slate-950 border border-slate-800 text-slate-200 text-right rounded px-2 py-1 text-[10px] focus:outline-none focus:border-cyan-500 w-20 row-price">
                </td>
                <td class="py-3 px-4 text-slate-300 mono-text">${item.barcode || '<span class="text-slate-600 italic">None</span>'}</td>
                <td class="py-3 px-4 text-center">
                    <input type="number" name="items[${index}][quantity]" value="${item.quantity}" min="1" class="bg-slate-950 border border-slate-800 text-slate-200 text-center rounded px-2 py-1 text-[10px] focus:outline-none focus:border-cyan-500 w-12 row-qty">
                    <span class="text-[9px] text-slate-500 block mt-1">${item.free_quantity > 0 ? `+ ${item.free_quantity} Free` : ''}</span>
                </td>
                <td class="py-3 px-4 text-right font-bold text-slate-200 mono-text row-total">${item.total.toFixed(2)}</td>
                <td class="py-3 px-4 text-center">
                    <button type="button" class="text-rose-500 hover:text-rose-400 text-xs font-bold btn-delete"><i class="fa-solid fa-trash"></i></button>
                </td>
            `;

            const rowCost = tr.querySelector('.row-cost');
            const rowPrice = tr.querySelector('.row-price');
            const rowQty = tr.querySelector('.row-qty');
            const rowTotal = tr.querySelector('.row-total');
            const btnDelete = tr.querySelector('.btn-delete');

            function updateRowTotal() {
                const cost = parseFloat(rowCost.value) || 0;
                const qty = parseInt(rowQty.value) || 0;
                const singleDisc = item.single_discount_amount || 0;
                const tot = Math.max(0, (cost - singleDisc) * qty);

                rowTotal.textContent = tot.toFixed(2);
                item.buying_price = cost;
                item.price = parseFloat(rowPrice.value) || 0;
                item.quantity = qty;
                item.total = tot;

                calculateCheckoutTotals();
            }

            rowCost.addEventListener('input', updateRowTotal);
            rowPrice.addEventListener('input', updateRowTotal);
            rowQty.addEventListener('input', updateRowTotal);

            btnDelete.addEventListener('click', function() {
                cart.splice(index, 1);
                renderCartTable();
            });

            selectedProductsTable.appendChild(tr);
        });

        calculateCheckoutTotals();
    }

    // Checkout totals calculations
    function calculateCheckoutTotals() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += item.total;
        });

        lblSubtotal.textContent = subtotal.toFixed(2);
        document.getElementById('subtotalInput').value = subtotal.toFixed(2);

        const discPercent = parseFloat(finalDiscountPercent.value) || 0;
        let discFlat = parseFloat(finalDiscountFlat.value) || 0;
        const charges = parseFloat(serviceCharges.value) || 0;

        const discountable = subtotal;
        const grandTotal = Math.max(0, discountable - discFlat) + charges;

        lblGrandTotal.textContent = grandTotal.toFixed(2);
        grandTotalInput.value = grandTotal.toFixed(2);

        // Auto update paid amount if paid Yes
        if (isPaidSelect.value === 'Yes') {
            paidAmount.value = grandTotal.toFixed(2);
        }
    }

    finalDiscountPercent.addEventListener('input', function() {
        let subtotal = 0;
        cart.forEach(item => { subtotal += item.total; });
        const pct = parseFloat(finalDiscountPercent.value) || 0;
        finalDiscountFlat.value = (subtotal * (pct / 100)).toFixed(2);
        calculateCheckoutTotals();
    });

    finalDiscountFlat.addEventListener('input', function() {
        let subtotal = 0;
        cart.forEach(item => { subtotal += item.total; });
        const flat = parseFloat(finalDiscountFlat.value) || 0;
        if (subtotal > 0) {
            finalDiscountPercent.value = ((flat / subtotal) * 100).toFixed(2);
        } else {
            finalDiscountPercent.value = 0;
        }
        calculateCheckoutTotals();
    });

    serviceCharges.addEventListener('input', calculateCheckoutTotals);
    
    isPaidSelect.addEventListener('change', function() {
        if (this.value === 'Yes') {
            let total = parseFloat(lblGrandTotal.textContent) || 0;
            paidAmount.value = total.toFixed(2);
            paidAmount.readOnly = false;
        } else {
            paidAmount.value = '0.00';
            paidAmount.readOnly = true;
        }
    });

    // Form submission validation
    document.getElementById('grnForm').addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Please add at least one product to the GRN order.');
            return;
        }
    });
});
</script>
@endsection
