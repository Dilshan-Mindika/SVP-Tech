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

<div class="card glass" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>New Payment Details</h3>
    </div>
    
    <div style="padding: 2rem;">
        
        <!-- Summary Box -->
        <div style="background: rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid rgba(255, 255, 255, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 1rem;">
                <div>
                    <span style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.3rem;">Total Invoice Amount</span>
                    <span style="font-size: 1.1rem; font-weight: 600;">LKR {{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div style="text-align: right;">
                    <span style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.3rem;">Balance Due</span>
                    <span style="font-size: 1.4rem; font-weight: 700; color: #ef4444;">LKR {{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: var(--text-muted);">
                <i class="fas fa-info-circle"></i>
                <span>Recording a payment will update the invoice status and customer ledger automatically.</span>
            </div>
        </div>

        <form action="{{ route('payments.store', $invoice->id) }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;">Payment Amount (LKR)</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-weight: 600;">LKR</span>
                    <input type="number" step="0.01" name="amount" max="{{ $invoice->balance_due }}" value="{{ $invoice->balance_due }}" required 
                           style="width: 100%; padding: 1rem 1rem 1rem 3.5rem; font-size: 1.2rem; font-weight: 600; border-radius: 8px; border: 1px solid var(--border-color); background: rgba(0, 0, 0, 0.3); color: #fff;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.9rem;">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required 
                           class="search-input" style="width: 100%; border: 1px solid var(--border-color); background: rgba(0, 0, 0, 0.2);">
                </div>

                <div>
                    <label style="display: block; color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.9rem;">Payment Method</label>
                    <select name="payment_method" required class="search-input" style="width: 100%; border: 1px solid var(--border-color); background: rgba(0, 0, 0, 0.2);">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.9rem;">Reference / Cheque No (Optional)</label>
                <input type="text" name="reference_number" placeholder="Enter transaction Ref ID or Cheque No"
                       class="search-input" style="width: 100%; border: 1px solid var(--border-color); background: rgba(0, 0, 0, 0.2);">
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.9rem;">Notes (Optional)</label>
                <textarea name="notes" rows="3" placeholder="Any additional details..."
                          class="search-input" style="width: 100%; border: 1px solid var(--border-color); background: rgba(0, 0, 0, 0.2);"></textarea>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 1rem; font-size: 1.1rem; font-weight: 600;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> Confirm Payment
            </button>
        </form>
    </div>
</div>
@endsection
