@extends('layouts.app')

@section('title', 'New Direct Sale')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>New Direct Sale</h2>
        <p class="text-muted">Process a new sale transaction</p>
    </div>
    <a href="{{ route('invoices.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i> Back to Invoices
    </a>
</div>

<div class="card glass" x-data="salesForm()">
    <form action="{{ route('sales.store') }}" method="POST">
        @csrf
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Left Column: Items & Customer -->
            <div>
                <!-- Customer Selection -->
                <div style="margin-bottom: 2rem;">
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Customer</label>
                    <select name="customer_id" class="form-control" style="width: 100%; padding: 0.8rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius);">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Items List -->
                <h3 style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Sale Items</h3>
                
                <template x-for="(item, index) in items" :key="index">
                    <div style="display: grid; grid-template-columns: 3fr 1fr 1.5fr auto; gap: 1rem; align-items: end; margin-bottom: 1rem; background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px;">
                        <div>
                            <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted);" x-show="index === 0">Item Description</label>
                            <input type="text" :name="'items['+index+'][description]'" x-model="item.description" class="form-control" placeholder="Item Name / Description" required
                                style="width: 100%; padding: 0.6rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius);">
                            
                            <!-- Inventory Suggestions (Simple Datalist simulation or just text for now) -->
                            <div class="inventory-suggestions" style="display: flex; gap: 0.5rem; margin-top: 0.5rem; overflow-x: auto;">
                                @foreach($inventoryParts as $part)
                                    <button type="button" 
                                            @click="item.description = '{{ $part->name }}'; item.unit_price = {{ $part->selling_price }};"
                                            style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: var(--primary); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; white-space: nowrap;">
                                        {{ $part->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted);" x-show="index === 0">Qty</label>
                            <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" min="1" class="form-control" required
                                style="width: 100%; padding: 0.6rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius); text-align: center;">
                        </div>
                        <div>
                            <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted);" x-show="index === 0">Unit Price</label>
                            <input type="number" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" step="0.01" min="0" class="form-control" required
                                style="width: 100%; padding: 0.6rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius); text-align: right;">
                        </div>
                        <div>
                            <button type="button" @click="removeItem(index)" class="btn-danger" style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: none; width: 32px; height: 32px; border-radius: 4px; cursor: pointer;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addItem()" class="btn-secondary" style="width: 100%; margin-top: 1rem; border-style: dashed;">
                    <i class="fas fa-plus"></i> Add Another Item
                </button>
            </div>

            <!-- Right Column: Summary -->
            <div>
                <div style="background: rgba(0,0,0,0.3); padding: 1.5rem; border-radius: 12px; position: sticky; top: 1rem;">
                    <h3 style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1.5rem;">Sale Summary</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="color: var(--text-muted);">Total Items</span>
                        <span style="font-weight: 600;" x-text="items.reduce((acc, item) => acc + parseInt(item.quantity || 0), 0)">0</span>
                    </div>

                    <div style="border-top: 1px solid var(--border-color); margin: 1rem 0; padding-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 1.1rem; font-weight: 600;">Total Amount</span>
                            <span style="font-size: 1.5rem; font-weight: 700; color: var(--success);" x-text="'Rs. ' + calculateTotal().toFixed(2)">Rs. 0.00</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 1.5rem; padding: 1rem; font-size: 1.1rem;">
                        Complete Sale <i class="fas fa-check-circle" style="margin-left: 0.5rem;"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function salesForm() {
        return {
            items: [
                { description: '', quantity: 1, unit_price: 0 }
            ],
            addItem() {
                this.items.push({ description: '', quantity: 1, unit_price: 0 });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            calculateTotal() {
                return this.items.reduce((total, item) => {
                    return total + (parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0));
                }, 0);
            }
        }
    }
</script>
@endsection
