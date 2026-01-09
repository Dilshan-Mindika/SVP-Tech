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

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by Date Range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

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
            ->select('id', 'name', 'selling_price', 'cost_price', 'stock_quantity')
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('customers', 'nextSaleNumber', 'inventoryParts'));
    }

    public function storeSale(Request $request)
    {
        $request->validate([
            'customer_action' => 'required|in:existing,new',
            'customer_id' => 'required_if:customer_action,existing|nullable|exists:customers,id',
            'new_customer_name' => 'required_if:customer_action,new|nullable|string|max:255',
            'new_customer_phone' => 'required_if:customer_action,new|nullable|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // Determine Customer ID
            $customerId = $request->customer_id;

            if ($request->customer_action === 'new') {
                $customer = Customer::create([
                    'name' => $request->new_customer_name,
                    'phone' => $request->new_customer_phone,
                    'address' => $request->new_customer_address,
                    'type' => 'individual', // Default type
                ]);
                $customerId = $customer->id;
            }

            // 1. Create a RepairJob of type 'sale'
            $repairJob = RepairJob::create([
                'job_number' => 'SALE-' . strtoupper(uniqid()), // Temporary unique ID, or use sequence
                'customer_id' => $customerId,
                'technician_id' => auth()->id(), // Assigned to current user (admin/tech)
                'job_type' => 'sale',
                'repair_status' => 'completed',
                'payment_status' => 'pending', // Assuming this field exists or handled by invoice
                'fault_description' => 'Direct Sale', // Mandatory field, made nullable in migration but good to fill
                'completed_at' => now(),
                'invoice_generated' => true,
            ]);
            
            // 2. Add Items as JobInvoiceItems
            $totalRevenue = 0;
            $totalCost = 0;

            foreach ($request->items as $item) {
                $qty = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $unitCost = $item['unit_cost'] ?? 0;

                $repairJob->invoiceItems()->create([
                    'description' => $item['description'],
                    'quantity' => $qty,
                    'amount' => $unitPrice,
                    'unit_cost' => $unitCost,
                ]);

                $totalRevenue += ($qty * $unitPrice);
                $totalCost += ($qty * $unitCost);
            }
            
            // 3. Update Job Financials
            $repairJob->update([
                'final_price' => $totalRevenue,
                'parts_used_cost' => $totalCost, // Store cost for dashboard profit calc
            ]);

            // 4. Generate Invoice
            $profitMargin = $totalRevenue - $totalCost;

            Invoice::create([
                'repair_job_id' => $repairJob->id,
                'total_amount' => $totalRevenue,
                'parts_cost' => $totalCost, // For Sales, we treat Item Cost as Parts Cost
                'labor_cost' => 0,
                'profit_margin' => $profitMargin,
                'status' => 'unpaid', // Default
                'due_date' => now()->addDays(30),
            ]);
        });

        return redirect()->route('invoices.index')->with('success', 'Sale recorded and invoice generated.');
    }
}
