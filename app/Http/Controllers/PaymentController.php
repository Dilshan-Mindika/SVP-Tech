<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($invoice->balance_due + 0.01), // Allow small rounding diff
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,cheque',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            $invoice->payments()->create([
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);

            $invoice->recalculateStatus();
        });

        return back()->with('success', 'Payment recorded successfully.');
    }
}
