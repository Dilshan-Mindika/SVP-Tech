<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RepairJob;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Generate an invoice for a specific job.
     */
    public function generate(Request $request, RepairJob $job)
    {
        $type = $request->input('type', 'job'); // 'job' (estimate) or 'service' (final)

        // Calculate Costs
        $partsCost = $job->parts->sum(function($part) {
            return $part->part_cost * $part->quantity_used;
        });

        $laborCost = $job->labor_cost;
        $totalAmount = $partsCost + $laborCost;
        
        // If final service invoice, we might add a markup or tax here
        // For now, simple sum. Profit is calculated against this.
        
        $profit = $totalAmount - ($partsCost + $laborCost); // Currently 0 unless we add markup logic

        $invoice = Invoice::create([
            'repair_job_id' => $job->id,
            'invoice_type' => $type,
            'total_amount' => $totalAmount,
            'parts_cost' => $partsCost,
            'labor_cost' => $laborCost,
            'profit_margin' => $profit,
        ]);

        // Update Job timestamps
        if ($type === 'job') {
            $job->update(['job_invoice_generated_at' => now()]);
        } else {
            $job->update(['service_invoice_generated_at' => now()]);
        }

        return redirect()->route('invoices.show', $invoice->id);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['repairJob.customer', 'repairJob.technician', 'repairJob.parts']);
        return view('invoices.show', compact('invoice'));
    }

    public function preview(RepairJob $job)
    {
        // Check for existing invoice
        $invoice = $job->invoices()->latest()->first();
        
        // Calculate current costs from Job
        // Note: We use the manual 'parts_used_cost' from the Job if set, 
        // otherwise we could sum relation parts. User likely prefers the manual entry fields
        // from the Edit page since they are editing them "time to time".
        
        $partsCost = $job->parts_used_cost ?? 0;
        $laborCost = $job->labor_cost ?? 0;
        $totalAmount = $job->final_price ?? ($partsCost + $laborCost);
        $profit = $totalAmount - ($partsCost + $laborCost);

        if ($invoice) {
            // UPDATE existing invoice with latest figures
            $invoice->update([
                'total_amount' => $totalAmount,
                'parts_cost' => $partsCost,
                'labor_cost' => $laborCost,
                'profit_margin' => $profit,
            ]);
            
            return redirect()->route('invoices.show', $invoice->id);
        }

        // Create new if none exists
        $invoice = Invoice::create([
            'repair_job_id' => $job->id,
            'invoice_type' => 'job',
            'total_amount' => $totalAmount,
            'parts_cost' => $partsCost,
            'labor_cost' => $laborCost,
            'profit_margin' => $profit,
        ]);
        
        $job->update(['job_invoice_generated_at' => now()]);

        return redirect()->route('invoices.show', $invoice->id);
    }
}
