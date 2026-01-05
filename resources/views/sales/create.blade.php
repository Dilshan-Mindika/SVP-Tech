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
                    <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                        <button type="button" @click="customerType = 'existing'" 
                            :class="{'btn-primary': customerType === 'existing', 'btn-secondary': customerType !== 'existing'}"
                            style="flex: 1; padding: 0.5rem; border-radius: 8px;">
                            Existing Customer
                        </button>
                        <button type="button" @click="customerType = 'new'" 
                            :class="{'btn-primary': customerType === 'new', 'btn-secondary': customerType !== 'new'}"
                            style="flex: 1; padding: 0.5rem; border-radius: 8px;">
                            New Customer
                        </button>
                    </div>

                    <input type="hidden" name="customer_action" x-model="customerType">

                    <!-- Existing Customer Select -->
                    <div x-show="customerType === 'existing'">
                        <select name="customer_id" class="form-control" style="width: 100%; padding: 0.8rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius);">
                            <option value="">Select Existing Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- New Customer Fields -->
                    <div x-show="customerType === 'new'" style="display: grid; gap: 1rem; animation: fadeIn 0.3s ease;">
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Name</label>
                            <input type="text" name="new_customer_name" class="form-control" placeholder="Customer Name"
                                style="width: 100%; padding: 0.8rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius);">
                        </div>
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Phone</label>
                            <input type="text" name="new_customer_phone" class="form-control" placeholder="Phone Number"
                                style="width: 100%; padding: 0.8rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius);">
                        </div>
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Address (Optional)</label>
                            <input type="text" name="new_customer_address" class="form-control" placeholder="Address"
                                style="width: 100%; padding: 0.8rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius);">
                        </div>
                    </div>
                </div>

                <!-- Items List -->
                <h3 style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Sale Items</h3>
                
                <!-- Items Grid Header -->
                <div style="display: grid; grid-template-columns: 3fr 1fr 1.5fr 1.5fr auto; gap: 1rem; margin-bottom: 0.5rem; padding: 0 1rem;">
                    <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted); margin: 0;">Item Description</label>
                    <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted); margin: 0; text-align: center;">Qty</label>
                    <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted); margin: 0; text-align: right;">Cost (LKR)</label>
                    <label class="form-label" style="font-size: 0.8rem; color: var(--text-muted); margin: 0; text-align: right;">Selling (LKR)</label>
                    <div style="width: 32px;"></div>
                </div>
                
                <template x-for="(item, index) in items" :key="index">
                    <div style="display: grid; grid-template-columns: 3fr 1fr 1.5fr 1.5fr auto; gap: 1rem; align-items: start; margin-bottom: 0.5rem; background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px;">
                        <div>
                            <input type="text" :name="'items['+index+'][description]'" x-model="item.description" class="form-control" placeholder="Item Name / Description" required
                                style="width: 100%; padding: 0.6rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius);">
                            
                            <!-- Inventory Suggestions -->
                            <div class="inventory-suggestions" style="display: flex; gap: 0.5rem; margin-top: 0.5rem; flex-wrap: wrap;">
                                @foreach($inventoryParts as $part)
                                    <button type="button" 
                                            @click="item.description = '{{ $part->name }}'; item.unit_price = {{ $part->selling_price }}; item.unit_cost = {{ $part->cost_price ?? 0 }};"
                                            style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: var(--primary); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; white-space: nowrap; transition: all 0.2s;">
                                        {{ $part->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" min="1" class="form-control" required
                                style="width: 100%; padding: 0.6rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius); text-align: center;">
                        </div>
                        <div>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 0.7rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;">Rs.</span>
                                <input type="number" :name="'items['+index+'][unit_cost]'" x-model="item.unit_cost" step="0.01" min="0" class="form-control"
                                    style="width: 100%; padding: 0.6rem 0.6rem 0.6rem 2rem; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color); color: var(--text-muted); border-radius: var(--radius); text-align: right;" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 0.7rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;">Rs.</span>
                                <input type="number" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" step="0.01" min="0" class="form-control" required
                                    style="width: 100%; padding: 0.6rem 0.6rem 0.6rem 2rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius); text-align: right;">
                            </div>
                        </div>
                        <div style="padding-top: 0.2rem;">
                            <button type="button" @click="removeItem(index)" class="btn-remove" title="Remove Item">
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
            customerType: 'existing',
            items: [
                { description: '', quantity: 1, unit_price: 0, unit_cost: 0 }
            ],
            addItem() {
                this.items.push({ description: '', quantity: 1, unit_price: 0, unit_cost: 0 });
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

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .btn-remove {
        background: rgba(239, 68, 68, 0.1);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.2);
        width: 36px;
        height: 36px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .btn-remove:hover {
        background: rgba(239, 68, 68, 0.2);
        transform: scale(1.05);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.4);
        box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1), 0 2px 4px -1px rgba(239, 68, 68, 0.06);
    }
    
    .btn-remove:active {
        transform: scale(0.95);
    }

    .btn-remove i {
        font-size: 0.9rem;
        transition: transform 0.2s;
    }

    .btn-remove:hover i {
        transform: rotate(15deg);
    }
</style>
@endsection
