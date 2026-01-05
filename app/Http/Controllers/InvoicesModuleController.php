<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RepairJob;
use App\Models\Customer;
use App\Models\JobInvoiceItem;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoicesModuleController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['repairJob.customer'])
            ->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('repairJob', function($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    public function createSale()
    {
        $customers = Customer::orderBy('name')->get();
        // Generate a temporary job number for display if needed, or handle in store
        $nextId = RepairJob::max('id') + 1;
        $nextSaleNumber = 'SALE-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        
        $inventoryParts = \App\Models\Part::where('stock_quantity', '>', 0)
            ->select('id', 'name', 'selling_price', 'stock_quantity')
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('customers', 'nextSaleNumber', 'inventoryParts'));
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Create a RepairJob of type 'sale'
            $repairJob = RepairJob::create([
                'job_number' => 'SALE-' . strtoupper(uniqid()), // Temporary unique ID, or use sequence
                'customer_id' => $request->customer_id,
                'technician_id' => auth()->id(), // Assigned to current user (admin/tech)
                'job_type' => 'sale',
                'repair_status' => 'completed',
                'payment_status' => 'pending', // Assuming this field exists or handled by invoice
                'fault_description' => 'Direct Sale', // Mandatory field, made nullable in migration but good to fill
                'completed_at' => now(),
                'invoice_generated' => true,
            ]);
            
            // 2. Add Items as JobInvoiceItems
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $repairJob->invoiceItems()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'amount' => $item['unit_price'],
                ]);
                $totalAmount += ($item['quantity'] * $item['unit_price']);
            }
            
            // 3. Update Job Financials
            $repairJob->update(['final_price' => $totalAmount]);

            // 4. Generate Invoice
            Invoice::create([
                'repair_job_id' => $repairJob->id,
                'total_amount' => $totalAmount,
                'status' => 'unpaid', // Default
                'due_date' => now()->addDays(30),
            ]);
        });

        return redirect()->route('invoices.index')->with('success', 'Sale recorded and invoice generated.');
    }
}
