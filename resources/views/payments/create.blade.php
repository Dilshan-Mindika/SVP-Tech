@extends('layouts.app')

@section('title', 'Record Payment - Invoice #' . $invoice->id)

@section('content')
<div class="page-header">
    <div class="header-content">
        <h2>Record Payment</h2>
        <p class="text-muted">Invoice #{{ 'INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>
    <a href="{{ url()->previous() }}" class="btn-secondary">
        <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i> Cancel
    </a>
</div>

<div class="card glass" style="max-width: 650px; margin: 0 auto;">
    <div class="card-header">
        <h3>New Payment Details</h3>
    </div>
    
    <div style="padding: 2rem;" x-data="{ 
        amount: {{ $invoice->balance_due }}, 
        maxAmount: {{ $invoice->balance_due }},
        get isOver() { return parseFloat(this.amount) > parseFloat(this.maxAmount); },
        get excessAmount() { return (parseFloat(this.amount) - parseFloat(this.maxAmount)).toFixed(2); }
    }">
        
        <!-- Summary Box -->
        <div class="summary-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 1rem;">
                <div>
                    <span class="block text-sm text-gray-500 mb-1">Total Invoice Amount</span>
                    <span style="font-size: 1.1rem; font-weight: 600;">LKR {{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div style="text-align: right;">
                    <span class="block text-sm text-gray-500 mb-1">Balance Due</span>
                    <span class="text-danger" style="font-size: 1.4rem; font-weight: 700;">LKR {{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem;" class="text-muted">
                <i class="fas fa-info-circle"></i>
                <span>Recording a payment will update the invoice status and customer ledger automatically.</span>
            </div>
        </div>

        <form action="{{ route('payments.store', $invoice->id) }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label font-medium">Payment Amount (LKR) <span class="text-danger">*</span></label>
                <div class="amount-input-wrapper" style="position: relative;">
                    <span style="position: absolute; left: 1rem; top: 18px; font-weight: 600; z-index: 10;" class="text-muted">LKR</span>
                    <input type="number" step="0.01" name="amount" x-model="amount" required 
                           :class="{'border-blue-500 text-blue-400': isOver}"
                           style="width: 100%; padding: 1rem 1rem 1rem 3.5rem; font-size: 1.2rem; font-weight: 600; border-radius: 8px;">
                </div>
                
                <!-- Credit Message -->
                <div x-show="isOver" x-transition class="mt-2 text-sm text-blue-400 font-bold flex items-center gap-2" style="margin-top: 0.5rem;">
                    <i class="fas fa-wallet"></i>
                    <span>Excess of LKR <span x-text="excessAmount"></span> will be credited to the customer's account.</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required 
                           class="form-input">
                </div>

                <div>
                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <div style="position: relative;">
                        <select name="payment_method" required class="form-input" style="appearance: none; cursor: pointer;">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                        <i class="fas fa-chevron-down" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--text-muted);"></i>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Reference / Cheque No (Optional)</label>
                <input type="text" name="reference_number" placeholder="Enter transaction Ref ID or Cheque No"
                       class="form-input">
            </div>

            <div style="margin-bottom: 2rem;">
                <label class="form-label">Notes (Optional)</label>
                <textarea name="notes" rows="3" placeholder="Any additional details..."
                          class="form-input"></textarea>
            </div>

            <button type="submit" class="btn-primary" 
                    style="width: 100%; justify-content: center; padding: 1rem; font-size: 1.1rem; font-weight: 600;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> Confirm Payment
            </button>
        </form>
    </div>
</div>
</div>

    <style>
        /* Base Dark Mode Styles */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .summary-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .amount-input-wrapper input {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .form-input {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.2s;
            color-scheme: dark; /* Force native controls to dark mode */
        }
        
        /* Ensure options are visible */
        .form-input option {
            background-color: #1f2937; /* Gray-800 */
            color: #ffffff;
        }

        .form-label {
            display: block;
            color: var(--text-muted); 
            margin-bottom: 0.5rem; 
            font-size: 0.9rem;
        }

        /* Light Mode Overrides */
        [data-theme="light"] .glass-card {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        [data-theme="light"] .summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        
        [data-theme="light"] .summary-box span, 
        [data-theme="light"] .summary-box i {
            color: #475569 !important; /* Slate-600 */
        }
        
        [data-theme="light"] .summary-box .text-danger {
            color: #ef4444 !important;
        }

        [data-theme="light"] .amount-input-wrapper input {
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        
        [data-theme="light"] .amount-input-wrapper span {
            color: #64748b !important;
        }

        [data-theme="light"] .form-input {
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            color-scheme: light; /* Force native controls to light mode */
        }
        
        [data-theme="light"] .form-label {
            color: #475569 !important;
        }

        [data-theme="light"] option {
            background: #fff;
            color: #1e293b;
        }

        /* Shared Interactions */
        .form-input:focus, .amount-input-wrapper input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        [data-theme="light"] .form-input:focus, 
        [data-theme="light"] .amount-input-wrapper input:focus {
            background: #fff;
        }
        
        /* Calendar icon handled native by color-scheme property */
        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
        }
    </style>
@endsection
