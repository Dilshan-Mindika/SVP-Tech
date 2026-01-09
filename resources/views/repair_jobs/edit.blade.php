@extends('layouts.app')

@section('title', 'Edit Repair Job #' . $repairJob->id)

@section('content')
<div class="page-header">
    <h2>Edit Repair Job #{{ $repairJob->id }}</h2>
</div>

<div class="card glass" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <form action="{{ route('repair-jobs.update', $repairJob->id) }}" method="POST"
          x-data="{ 
              status: '{{ $repairJob->repair_status }}',
              parts: {{ $repairJob->parts_used_cost ?? 0 }}, 
              labor: {{ $repairJob->labor_cost ?? 0 }}, 
              price: {{ $repairJob->final_price ?? 0 }},
              get profit() { return this.price - (Number(this.parts) + Number(this.labor)); }
          }">
        @csrf
        @method('PUT')
        
        <div class="grid-2">
            <!-- Status & Tech Assignment -->
            <div class="form-group">
                <label>Job Status</label>
                <select name="repair_status" class="status-select" x-model="status" :class="'status-' + status">
                    <option value="pending" {{ $repairJob->repair_status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ $repairJob->repair_status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="waiting_for_parts" {{ $repairJob->repair_status == 'waiting_for_parts' ? 'selected' : '' }}>Waiting for Parts</option>
                    <option value="completed" {{ $repairJob->repair_status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="delivered" {{ $repairJob->repair_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $repairJob->repair_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label>Job Number (Customizable)</label>
                <input type="text" name="job_number" value="{{ $repairJob->job_number }}" class="form-control" style="font-family: monospace; font-size: 1.1rem; letter-spacing: 1px; font-weight: 600;">
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Assign Technician</label>
                <select name="technician_id" class="form-control">
                    <option value="">-- Unassigned --</option>
                    @foreach(\App\Models\Technician::with('user')->get() as $tech)
                        <option value="{{ $tech->id }}" {{ $repairJob->technician_id == $tech->id ? 'selected' : '' }}>
                            {{ $tech->user->name }} ({{ $tech->specialty }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <h3 class="section-title" style="margin-top: 2rem;">Job Details</h3>
        
        <div class="grid-2">
            <div class="form-group">
                <label>Laptop Brand</label>
                <input type="text" name="laptop_brand" value="{{ $repairJob->laptop_brand }}" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label>Laptop Model</label>
                <input type="text" name="laptop_model" value="{{ $repairJob->laptop_model }}" class="form-control" readonly>
            </div>
        </div>

        <div class="form-group">
            <label>Fault Description</label>
            <textarea name="fault_description" class="form-control" rows="3">{{ $repairJob->fault_description }}</textarea>
        </div>

        <div class="form-group">
            <label>Work Done / Parts Added (Notes)</label>
            <textarea name="repair_notes" class="form-control" rows="3" placeholder="e.g. Replaced LCD Screen, Added 8GB RAM...">{{ $repairJob->repair_notes }}</textarea>
        </div>

        <!-- Hardware Specs Section -->
        <div style="margin-top: 2rem;" x-data="{ 
            showSpecs: {{ $repairJob->device_specs ? 'true' : 'false' }},
            specs: {{ json_encode($repairJob->device_specs ?? []) }} 
        }">
            <button type="button" @click="showSpecs = !showSpecs" class="btn-secondary" style="width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span class="font-bold"><i class="fas fa-microchip" style="margin-right: 0.5rem;"></i> Hardware Specifications (Optional)</span>
                <i class="fas" :class="showSpecs ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            
            <div x-show="showSpecs" x-transition class="card glass" style="padding: 1.5rem; background: rgba(0,0,0,0.1);">
                <div class="grid-2">
                    <div class="form-group">
                        <label>RAM (Memory)</label>
                        <input type="text" name="device_specs[ram]" x-model="specs.ram" class="form-control" placeholder="e.g. 8GB DDR4">
                    </div>
                    <div class="form-group">
                        <label>Storage (HDD/SSD)</label>
                        <input type="text" name="device_specs[storage]" x-model="specs.storage" class="form-control" placeholder="e.g. 512GB NVMe SSD">
                    </div>
                    <div class="form-group">
                        <label>Processor (CPU)</label>
                        <input type="text" name="device_specs[cpu]" x-model="specs.cpu" class="form-control" placeholder="e.g. Intel Core i5">
                    </div>
                    <div class="form-group">
                        <label>Display</label>
                        <input type="text" name="device_specs[display]" x-model="specs.display" class="form-control" placeholder="e.g. 15.6 inch FHD, No scratches">
                    </div>
                    <div class="form-group">
                        <label>Battery Condition</label>
                        <input type="text" name="device_specs[battery]" x-model="specs.battery" class="form-control" placeholder="e.g. Good, Holding charge">
                    </div>
                    <div class="form-group">
                        <label>Keyboard / Touchpad</label>
                        <input type="text" name="device_specs[keyboard]" x-model="specs.keyboard" class="form-control" placeholder="e.g. All keys working">
                    </div>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label>Other Components / Notes</label>
                    <textarea name="device_specs[other]" x-model="specs.other" class="form-control" rows="2" placeholder="Any serial numbers or other component details..."></textarea>
                </div>
            </div>
        </div>

        <h3 class="section-title" style="margin-top: 2rem;" x-show="status === 'completed'" x-transition x-cloak>Financials & Completion</h3>

        <div x-data="{
            expenses: {{ $repairJob->expenses->toJson() ?? '[]' }},
            invoiceItems: {{ $repairJob->invoiceItems->toJson() ?? '[]' }},
            availableParts: {{ $inventoryParts->toJson() ?? '[]' }},
            
            addExpense() { this.expenses.push({ description: '', amount: 0 }); },
            removeExpense(index) { this.expenses.splice(index, 1); },
            
            addInvoiceItem() { this.invoiceItems.push({ description: '', quantity: 1, amount: 0 }); },
            removeInvoiceItem(index) { this.invoiceItems.splice(index, 1); },
            
            checkPart(item) {
                let part = this.availableParts.find(p => p.name === item.description);
                if (part) {
                    item.amount = part.selling_price;
                }
            },
            
            get totalExpenses() { return this.expenses.reduce((sum, item) => sum + Number(item.amount), 0); },
            get totalRevenue() { return this.invoiceItems.reduce((sum, item) => sum + (Number(item.amount) * Number(item.quantity)), 0); },
            get netProfit() { return this.totalRevenue - this.totalExpenses; }
        }" x-show="status === 'completed'" x-transition x-cloak>

            <!-- Internal Expenses (Red Section) -->
            <div class="section-box-danger">
                <h4>
                    <i class="fas fa-file-invoice-dollar" style="margin-right: 0.5rem;"></i> Internal Job Expenses (Hidden from Invoice)
                </h4>
                
                <table class="w-full mb-4" style="width: 100%; border-collapse: separate; border-spacing: 0 0.5rem;">
                    <thead>
                        <tr style="text-align: left; color: var(--text-muted); font-size: 0.85rem;">
                            <th style="padding-bottom: 0.5rem;">Description (e.g. Bought IC, RAM)</th>
                            <th style="width: 150px; padding-bottom: 0.5rem;">Cost (LKR)</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(expense, index) in expenses" :key="index">
                            <tr>
                                <td style="padding-right: 1rem;">
                                    <input type="text" :name="'expenses['+index+'][description]'" x-model="expense.description" class="form-control" placeholder="Expense Description" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" :name="'expenses['+index+'][amount]'" x-model="expense.amount" class="form-control" required>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" @click="removeExpense(index)" style="color: #ef4444; background: none; border: none; cursor: pointer;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <button type="button" @click="addExpense()" style="color: var(--danger); background: rgba(239, 68, 68, 0.1); border: 1px dashed rgba(239, 68, 68, 0.3); padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">
                        <i class="fas fa-plus"></i> Add Expense
                    </button>
                    <div style="text-align: right; color: var(--danger);">
                        <small>Total Expenses:</small> <strong style="font-size: 1.1rem;">LKR <span x-text="totalExpenses.toFixed(2)"></span></strong>
                    </div>
                </div>
            </div>

            <!-- Billable Invoice Items (Green Section) -->
            <div class="section-box-success">
                <h4>
                    <i class="fas fa-receipt" style="margin-right: 0.5rem;"></i> Billable Invoice Items (Visible to Customer)
                </h4>

                <datalist id="inventoryList">
                    <template x-for="part in availableParts">
                        <option :value="part.name" x-text="part.name + ' (Stock: ' + part.stock_quantity + ')'"></option>
                    </template>
                </datalist>

                <table class="w-full mb-4" style="width: 100%; border-collapse: separate; border-spacing: 0 0.5rem;">
                    <thead>
                        <tr style="text-align: left; color: var(--text-muted); font-size: 0.85rem;">
                            <th style="padding-bottom: 0.5rem;">Service / Item Description</th>
                            <th style="width: 100px; padding-bottom: 0.5rem; text-align: center;">Qty</th>
                            <th style="width: 150px; padding-bottom: 0.5rem;">Unit Price (LKR)</th>
                            <th style="width: 120px; padding-bottom: 0.5rem; text-align: right;">Total</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in invoiceItems" :key="index">
                            <tr>
                                <td style="padding-right: 1rem;">
                                    <input type="text" :name="'invoice_items['+index+'][description]'" x-model="item.description" @change="checkPart(item)" list="inventoryList" class="form-control" placeholder="Service Charge / Part Name" required>
                                </td>
                                <td>
                                    <input type="number" :name="'invoice_items['+index+'][quantity]'" x-model="item.quantity" class="form-control" style="text-align: center;" min="1" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" :name="'invoice_items['+index+'][amount]'" x-model="item.amount" class="form-control" required>
                                </td>
                                <td style="text-align: right; color: var(--text-main); padding-right: 0.5rem;">
                                    <span x-text="(item.quantity * item.amount).toFixed(2)"></span>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" @click="removeInvoiceItem(index)" style="color: #ef4444; background: none; border: none; cursor: pointer;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <button type="button" @click="addInvoiceItem()" style="color: var(--success); background: rgba(34, 197, 94, 0.1); border: 1px dashed rgba(34, 197, 94, 0.3); padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-size: 0.9rem;">
                        <i class="fas fa-plus"></i> Add Billable Item / Part
                    </button>
                    <div style="text-align: right; color: var(--success);">
                        <small>Total Billable:</small> <strong style="font-size: 1.1rem;">LKR <span x-text="totalRevenue.toFixed(2)"></span></strong>
                    </div>
                </div>
            </div>

            <!-- Profit Summary -->
            <div class="grid-3">
                <div class="summary-box">
                    <label style="color: var(--text-muted); font-size: 0.8rem;">Total Expenses</label>
                    <div style="font-size: 1.25rem; font-weight: 600; color: #fca5a5;">
                        LKR <span x-text="totalExpenses.toFixed(2)"></span>
                    </div>
                </div>
                <div class="summary-box">
                    <label style="color: var(--text-muted); font-size: 0.8rem;">Total Billable</label>
                    <div style="font-size: 1.25rem; font-weight: 600; color: #86efac;">
                        LKR <span x-text="totalRevenue.toFixed(2)"></span>
                    </div>
                </div>
                <div class="summary-box" style="border: 1px solid var(--border-glass);">
                    <label style="color: var(--text-main); font-size: 0.9rem; font-weight: 600;">Net Profit</label>
                    <div style="font-size: 1.5rem; font-weight: 700;" :style="{ color: netProfit >= 0 ? '#4ade80' : '#ef4444' }">
                        LKR <span x-text="netProfit.toFixed(2)"></span>
                    </div>
                </div>
            </div>

        </div>

        <div class="form-actions" style="margin-top: 2rem;">
            <a href="{{ route('repair-jobs.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Job</button>
        </div>
    </form>
</div>

@endsection
