<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function create(Invoice $invoice)
    {
        return view('payments.create', compact('invoice'));
    }

    public function store(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($invoice->balance_due + 0.01), // Allow small rounding diff
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $excessAmount = 0;
        $message = 'Payment recorded successfully.';

        DB::transaction(function () use ($request, $invoice, &$excessAmount) {
            // Check for overpayment
            if ($request->amount > $invoice->balance_due) {
                $excessAmount = $request->amount - $invoice->balance_due;
                
                // Limit the recorded payment amount to the balance due (to close the invoice cleanly)
                // Or record full amount? -> User wants "save on system" (credit).
                // Strategy: 
                // 1. Record the FULL payment on the invoice (so the transaction history is accurate: "Customer paid 5000").
                // 2. The Invoice logic will set balance to 0 (or negative).
                // 3. We explicitly add the excess to the customer credit.
                
                $invoice->repairJob->customer->increment('credit_balance', $excessAmount);
            }

            $invoice->payments()->create([
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes . ($excessAmount > 0 ? " (Includes Credit: LKR {$excessAmount})" : ''),
            ]);

            $invoice->recalculateStatus();
        });

        if ($excessAmount > 0) {
            $message .= ' LKR ' . number_format($excessAmount, 2) . ' has been credited to the customer account.';
        }

        return redirect()->route('invoices.show', $invoice->id)->with('success', $message);
    }
}
