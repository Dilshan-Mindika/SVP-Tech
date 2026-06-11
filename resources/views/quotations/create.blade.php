@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-120px)] flex flex-col md:flex-row gap-6">
    
    <!-- Left Column: Product Selector (Catalog) -->
    <div class="flex-1 bg-slate-900 border border-slate-800 rounded-xl p-5 flex flex-col h-full overflow-hidden">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 shrink-0">
            <h3 class="orbitron-title text-sm font-black text-slate-200 uppercase tracking-widest">PRODUCT CATALOG</h3>
            <!-- Search bar -->
            <input type="text" id="catalogSearch" placeholder="Filter by Name or SKU..." class="bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500 w-48 transition-colors">
        </div>

        <!-- Products List -->
        <div class="flex-grow overflow-y-auto mt-4 pr-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3" id="catalogGrid">
            @foreach($products as $prod)
                <div class="product-card bg-slate-950 border border-slate-850 hover:border-cyan-500/40 p-3 rounded-lg flex flex-col justify-between transition-all cursor-pointer group"
                     data-id="{{ $prod->id }}"
                     data-name="{{ $prod->name }}"
                     data-sku="{{ $prod->sku }}"
                     data-price="{{ $prod->price }}"
                     data-stock="{{ $prod->stock }}">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[9px] bg-slate-800 text-slate-400 font-bold px-1.5 py-0.5 rounded tracking-wide uppercase">{{ $prod->sku }}</span>
                            <span class="text-[9px] font-bold text-slate-400">Stock: {{ $prod->stock }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-200 mt-2 line-clamp-2 group-hover:text-cyan-400 transition-colors">{{ $prod->name }}</h4>
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-2 border-t border-slate-900/60">
                        <span class="text-xs font-black text-cyan-400 mono-text">Rs. {{ number_format($prod->price, 2) }}</span>
                        <button type="button" class="text-[10px] bg-cyan-500/10 text-cyan-400 font-bold px-2 py-1 rounded hover:bg-cyan-500 hover:text-slate-950 transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-plus"></i>
                            <span>ADD</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Right Column: Cart and Customer/Quote Details -->
    <div class="w-full md:w-96 bg-slate-900 border border-slate-800 rounded-xl p-5 flex flex-col h-full overflow-hidden shrink-0">
        <h3 class="orbitron-title text-sm font-black text-slate-200 uppercase tracking-widest pb-4 border-b border-slate-800 shrink-0">QUOTATION BUILDER</h3>

        <form action="{{ route('quotations.store') }}" method="POST" id="quoteForm" class="flex-grow flex flex-col justify-between overflow-hidden mt-4">
            @csrf
            
            <!-- Cart Items List -->
            <div class="flex-grow overflow-y-auto pr-1 space-y-3 min-h-[150px]" id="cartContainer">
                <div class="h-full flex items-center justify-center text-slate-500 text-xs border border-dashed border-slate-850 rounded-lg p-6 text-center" id="emptyCartMessage">
                    <div>
                        <i class="fa-solid fa-file-invoice text-2xl mb-2 opacity-30"></i>
                        <p>Select products from catalog to build client quotation.</p>
                    </div>
                </div>
            </div>

            <!-- Customer & Quote Details -->
            <div class="border-t border-slate-800 pt-4 mt-4 space-y-3 shrink-0">
                <!-- Customer Selection -->
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Customer Selection</label>
                    <select name="customer_id" id="customerSelect" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500">
                        <option value="">-- Guest / Manual Entry --</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Guest Customer Manual Inputs -->
                <div id="manualCustomerDiv" class="space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[9px] text-slate-500 uppercase tracking-wider font-bold block mb-0.5">Guest Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="customer_name" id="manualName" placeholder="Client name" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                        <div>
                            <label class="text-[9px] text-slate-500 uppercase tracking-wider font-bold block mb-0.5">Phone Number <span class="text-rose-500">*</span></label>
                            <input type="text" name="customer_phone" id="manualPhone" placeholder="Phone" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                        </div>
                    </div>
                </div>

                <!-- Valid Days & Tax -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Validity (Days)</label>
                        <input type="number" name="valid_days" value="14" min="1" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:border-cyan-500">
                    </div>
                    <div class="flex flex-col justify-end">
                        <div class="flex items-center gap-2 pb-2">
                            <input type="checkbox" name="is_tax_quotation" id="taxCheckbox" value="1" class="rounded border-slate-800 bg-slate-950 text-cyan-500 focus:ring-cyan-500">
                            <label for="taxCheckbox" class="text-[10px] text-slate-300 font-bold uppercase tracking-wider select-none cursor-pointer">Add 15% VAT</label>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="text-[10px] text-slate-400 uppercase tracking-wider font-bold block mb-1">Additional Terms / Notes</label>
                    <textarea name="notes" placeholder="Delivery terms, warranty statements, etc." rows="2" class="w-full bg-slate-950 border border-slate-800 text-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-cyan-500 resize-none"></textarea>
                </div>

                <!-- Calculations -->
                <div class="bg-slate-950 border border-slate-850 p-3 rounded-lg space-y-1.5 text-xs">
                    <div class="flex items-center justify-between text-slate-400">
                        <span>Items Subtotal:</span>
                        <span class="mono-text font-semibold text-slate-300" id="lblSubtotal">Rs. 0.00</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-400">
                        <span>Estimated Tax (VAT):</span>
                        <span class="mono-text font-semibold text-slate-300" id="lblTax">Rs. 0.00</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-800 pt-1.5 font-bold text-sm text-cyan-400">
                        <span>Estimated Total:</span>
                        <span class="mono-text text-base font-black" id="lblTotal">Rs. 0.00</span>
                    </div>
                </div>

                <!-- Create Quote Action -->
                <button type="submit" class="w-full py-2.5 bg-cyan-500 text-slate-950 font-black rounded-lg text-xs uppercase tracking-widest transition-all hover:bg-cyan-400 shadow-neon-cyan flex items-center justify-center gap-2">
                    <i class="fa-solid fa-file-export"></i>
                    <span>GENERATE ESTIMATE / QUOTE</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const catalogSearch = document.getElementById('catalogSearch');
        const catalogGrid = document.getElementById('catalogGrid');
        const cartContainer = document.getElementById('cartContainer');
        const emptyCartMessage = document.getElementById('emptyCartMessage');
        const customerSelect = document.getElementById('customerSelect');
        const manualCustomerDiv = document.getElementById('manualCustomerDiv');
        const manualName = document.getElementById('manualName');
        const manualPhone = document.getElementById('manualPhone');
        const taxCheckbox = document.getElementById('taxCheckbox');

        const lblSubtotal = document.getElementById('lblSubtotal');
        const lblTax = document.getElementById('lblTax');
        const lblTotal = document.getElementById('lblTotal');

        let cart = [];

        // Search Filter
        catalogSearch.addEventListener('input', function() {
            const filter = catalogSearch.value.toLowerCase();
            const cards = catalogGrid.getElementsByClassName('product-card');
            for (let card of cards) {
                const name = card.dataset.name.toLowerCase();
                const sku = card.dataset.sku.toLowerCase();
                if (name.includes(filter) || sku.includes(filter)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            }
        });

        // Add to Cart
        const cards = catalogGrid.getElementsByClassName('product-card');
        for (let card of cards) {
            card.addEventListener('click', function() {
                const id = parseInt(card.dataset.id);
                const name = card.dataset.name;
                const sku = card.dataset.sku;
                const price = parseFloat(card.dataset.price);
                const stock = parseInt(card.dataset.stock);

                const existing = cart.find(item => item.id === id);
                if (existing) {
                    existing.quantity++;
                } else {
                    cart.push({
                        id, name, sku, price, stock, quantity: 1
                    });
                }
                renderCart();
            });
        }

        // Render Cart
        function renderCart() {
            if (cart.length === 0) {
                emptyCartMessage.style.display = 'flex';
                const items = cartContainer.getElementsByClassName('cart-item');
                while(items.length > 0) items[0].remove();
                calculateTotals();
                return;
            }

            emptyCartMessage.style.display = 'none';
            const items = cartContainer.getElementsByClassName('cart-item');
            while(items.length > 0) items[0].remove();

            cart.forEach((item, index) => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'cart-item bg-slate-950 border border-slate-850 p-3 rounded-lg space-y-2 relative';
                
                const header = document.createElement('div');
                header.className = 'flex justify-between items-start pr-6';
                header.innerHTML = `
                    <div>
                        <h4 class="text-xs font-bold text-slate-200 line-clamp-1">${item.name}</h4>
                        <span class="text-[9px] text-slate-400 font-medium">${item.sku}</span>
                    </div>
                `;

                const delBtn = document.createElement('button');
                delBtn.type = 'button';
                delBtn.className = 'absolute right-3 top-3 text-slate-500 hover:text-rose-500 text-xs transition-colors';
                delBtn.innerHTML = '<i class="fa-solid fa-trash-can"></i>';
                delBtn.onclick = () => {
                    cart.splice(index, 1);
                    renderCart();
                };
                itemDiv.appendChild(delBtn);
                itemDiv.appendChild(header);

                const inputRow = document.createElement('div');
                inputRow.className = 'flex items-center justify-between gap-3 pt-1';
                
                const hidId = document.createElement('input');
                hidId.type = 'hidden';
                hidId.name = `items[${index}][product_id]`;
                hidId.value = item.id;
                itemDiv.appendChild(hidId);

                const qtyDiv = document.createElement('div');
                qtyDiv.className = 'flex items-center gap-1.5';
                qtyDiv.innerHTML = `
                    <button type="button" class="h-6 w-6 rounded bg-slate-800 text-slate-300 flex items-center justify-center text-xs hover:bg-slate-750">-</button>
                    <input type="number" name="items[${index}][quantity]" value="${item.quantity}" min="1" readonly class="w-10 h-6 text-center bg-slate-900 border border-slate-800 text-slate-200 text-xs rounded p-0">
                    <button type="button" class="h-6 w-6 rounded bg-slate-800 text-slate-300 flex items-center justify-center text-xs hover:bg-slate-750">+</button>
                `;

                const btns = qtyDiv.getElementsByTagName('button');
                const qtyInp = qtyDiv.getElementsByTagName('input')[0];
                btns[0].onclick = () => {
                    if (item.quantity > 1) {
                        item.quantity--;
                        qtyInp.value = item.quantity;
                        calculateTotals();
                    }
                };
                btns[1].onclick = () => {
                    item.quantity++;
                    qtyInp.value = item.quantity;
                    calculateTotals();
                };
                inputRow.appendChild(qtyDiv);

                const prcSpan = document.createElement('span');
                prcSpan.className = 'text-xs font-bold text-slate-200 mono-text';
                prcSpan.textContent = `Rs. ${(item.price * item.quantity).toFixed(2)}`;
                inputRow.appendChild(prcSpan);

                itemDiv.appendChild(inputRow);
                cartContainer.appendChild(itemDiv);
            });

            calculateTotals();
        }

        // Calculate Totals
        function calculateTotals() {
            let subtotal = 0;
            cart.forEach(item => {
                subtotal += (item.price * item.quantity);
            });

            const tax = taxCheckbox.checked ? subtotal * 0.15 : 0;
            const total = subtotal + tax;

            lblSubtotal.textContent = `Rs. ${subtotal.toFixed(2)}`;
            lblTax.textContent = `Rs. ${tax.toFixed(2)}`;
            lblTotal.textContent = `Rs. ${total.toFixed(2)}`;
        }

        // Hide/Show guest name inputs
        customerSelect.addEventListener('change', function() {
            if (customerSelect.value) {
                manualCustomerDiv.classList.add('hidden');
                manualName.required = false;
                manualPhone.required = false;
            } else {
                manualCustomerDiv.classList.remove('hidden');
                manualName.required = true;
                manualPhone.required = true;
            }
        });

        taxCheckbox.addEventListener('change', calculateTotals);
    });
</script>
@endsection
