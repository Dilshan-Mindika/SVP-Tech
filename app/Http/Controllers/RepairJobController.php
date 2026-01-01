<?php

namespace App\Http\Controllers;

use App\Models\RepairJob;
use App\Models\Customer;
use App\Models\Technician;
use Illuminate\Http\Request;

class RepairJobController extends Controller
{
    public function index(Request $request)
    {
        $query = RepairJob::with(['customer', 'technician'])->latest();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('repair_status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                  ->orWhere('laptop_brand', 'like', "%{$search}%")
                  ->orWhere('laptop_model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $jobs = $query->get();
        $technicians = Technician::with('user')->get(); 

        // Calculate Status Counts (respecting search if exists, otherwise global)
        $statusCountsQuery = RepairJob::query();
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $statusCountsQuery->where(function($q) use ($search) {
                $q->where('job_number', 'like', "%{$search}%")
                  ->orWhere('laptop_brand', 'like', "%{$search}%")
                  ->orWhere('laptop_model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $statusCounts = $statusCountsQuery->selectRaw('repair_status, count(*) as count')
            ->groupBy('repair_status')
            ->pluck('count', 'repair_status')
            ->toArray();

        $totalJobsCount = array_sum($statusCounts);

        return view('repair_jobs.index', compact('jobs', 'technicians', 'statusCounts', 'totalJobsCount'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $technicians = Technician::with('user')->get();
        
        // Auto-generate next Job ID
        $nextId = RepairJob::max('id') + 1;
        $nextJobNumber = 'PWCRJ' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        return view('repair_jobs.create', compact('customers', 'technicians', 'nextJobNumber'));
    }

    public function store(Request $request)
    {
        // Check if creating new customer inline
        if ($request->customer_type === 'new') {
            $request->validate([
                'new_customer_name' => 'required|string|max:255',
                'new_customer_phone' => 'required|string|max:20',
            ]);
            
            $customer = Customer::create([
                'name' => $request->new_customer_name,
                'phone' => $request->new_customer_phone,
                'email' => $request->new_customer_email,
                'address' => $request->new_customer_address,
            ]);
            
            $request->merge(['customer_id' => $customer->id]);
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'job_number' => 'required|string|unique:repair_jobs,job_number',
            'laptop_brand' => 'required|string',
            'laptop_model' => 'required|string',
            'serial_number' => 'nullable|string',
            'fault_description' => 'required|string',
            'technician_id' => 'nullable|exists:technicians,id', 
            'repair_notes' => 'nullable|string',
        ]);

        RepairJob::create($validated);

        return redirect()->route('repair-jobs.index')->with('success', 'Repair Job created successfully.');
    }

    public function show(RepairJob $repairJob)
    {
        return view('repair_jobs.show', compact('repairJob'));
    }

    public function edit(RepairJob $repairJob)
    {
        $repairJob->load(['expenses', 'invoiceItems']);
        $inventoryParts = \App\Models\Part::where('stock_quantity', '>', 0)
            ->select('name', 'selling_price', 'stock_quantity')
            ->orderBy('name')
            ->get();
            
        return view('repair_jobs.edit', compact('repairJob', 'inventoryParts'));
    }

    public function update(Request $request, RepairJob $repairJob)
    {
        $validated = $request->validate([
            'repair_status' => 'required|in:pending,in_progress,waiting_for_parts,completed,delivered,cancelled',
            'job_number' => 'required|string|unique:repair_jobs,job_number,' . $repairJob->id,
            'technician_id' => 'nullable|exists:technicians,id',
            'fault_description' => 'required|string',
            'repair_notes' => 'nullable|string',
            'expenses' => 'nullable|array',
            'expenses.*.description' => 'required|string',
            'expenses.*.amount' => 'required|numeric|min:0',
            'invoice_items' => 'nullable|array',
            'invoice_items.*.description' => 'required|string',
            'invoice_items.*.quantity' => 'required|integer|min:1',
            'invoice_items.*.amount' => 'required|numeric|min:0',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($repairJob, $validated, $request) {
            // 1. Sync Expenses (Internal Cost)
            $repairJob->expenses()->delete();
            $totalExpenses = 0;
            if ($request->has('expenses')) {
                foreach ($request->expenses as $expense) {
                    $repairJob->expenses()->create($expense);
                    $totalExpenses += $expense['amount'];
                }
            }

            // 2. Sync Invoice Items (Billable)
            $repairJob->invoiceItems()->delete();
            $totalBillable = 0;
            if ($request->has('invoice_items')) {
                foreach ($request->invoice_items as $item) {
                    $repairJob->invoiceItems()->create($item);
                    $totalBillable += ($item['amount'] * $item['quantity']);
                }
            }
            
            // 3. Update Job Details & Totals
            $updateData = [
                'repair_status' => $validated['repair_status'],
                'job_number' => $validated['job_number'],
                'technician_id' => $validated['technician_id'],
                'fault_description' => $validated['fault_description'],
                'repair_notes' => $validated['repair_notes'],
                
                // Calculated Financials
                'parts_used_cost' => $totalExpenses, // Internal Expenses Total
                'labor_cost' => 0, // Deprecated/Merged
                'final_price' => $totalBillable, // Total Invoice Amount
            ];

            if ($validated['repair_status'] === 'completed' && !$repairJob->completed_at) {
                $updateData['completed_at'] = now();
            }
    
            if ($validated['repair_status'] === 'delivered') {
                if (!$repairJob->delivered_at) $updateData['delivered_at'] = now();
                if (!$repairJob->completed_at) $updateData['completed_at'] = now();
            }

            $repairJob->update($updateData);
        });

        return redirect()->route('repair-jobs.index')->with('success', 'Repair Job updated successfully.');
    }

    public function assignTechnician(Request $request, RepairJob $job)
    {
        $validated = $request->validate([
            'technician_id' => 'nullable|exists:technicians,id',
        ]);

        $job->update(['technician_id' => $validated['technician_id']]);

        return back()->with('success', 'Technician assigned successfully.');
    }

    public function updateStatus(Request $request, RepairJob $job)
    {
        $validated = $request->validate([
            'repair_status' => 'required|in:pending,in_progress,waiting_for_parts,completed,delivered,cancelled',
        ]);

        $updateData = ['repair_status' => $validated['repair_status']];

        if ($validated['repair_status'] === 'completed' && !$job->completed_at) {
            $updateData['completed_at'] = now();
        }

        if ($validated['repair_status'] === 'delivered') {
            if (!$job->delivered_at) $updateData['delivered_at'] = now();
            if (!$job->completed_at) $updateData['completed_at'] = now(); // Ensure completed_at is set if delivered
        }

        $job->update($updateData);

        return back()->with('success', 'Job Status updated to ' . str_replace('_', ' ', $validated['repair_status']));
    }

    public function destroy(RepairJob $repairJob)
    {
        $repairJob->delete();
        return redirect()->route('repair-jobs.index');
    }
}
