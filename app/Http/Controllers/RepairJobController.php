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
        return view('repair_jobs.index', compact('jobs'));
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
        if ($request->customer_id === 'new') {
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
        return view('repair_jobs.edit', compact('repairJob'));
    }

    public function update(Request $request, RepairJob $repairJob)
    {
        $validated = $request->validate([
            'repair_status' => 'required|in:pending,in_progress,waiting_for_parts,completed,delivered,cancelled',
            'job_number' => 'required|string|unique:repair_jobs,job_number,' . $repairJob->id,
            'technician_id' => 'nullable|exists:technicians,id',
            'fault_description' => 'required|string',
            'repair_notes' => 'nullable|string',
            'parts_used_cost' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'final_price' => 'nullable|numeric|min:0',
        ]);

        $repairJob->update([
            'repair_status' => $validated['repair_status'],
            'job_number' => $validated['job_number'],
            'technician_id' => $validated['technician_id'],
            'fault_description' => $validated['fault_description'],
            'repair_notes' => $validated['repair_notes'],
            'parts_used_cost' => $validated['parts_used_cost'] ?? 0,
            'labor_cost' => $validated['labor_cost'] ?? 0,
            'final_price' => $validated['final_price'] ?? 0,
        ]);

        return redirect()->route('repair-jobs.index')->with('success', 'Repair Job updated successfully.');
    }

    public function updateStatus(Request $request, RepairJob $job)
    {
        $validated = $request->validate([
            'repair_status' => 'required|in:pending,in_progress,waiting_for_parts,completed,cancelled',
        ]);

        $job->update(['repair_status' => $validated['repair_status']]);

        return back()->with('success', 'Job Status updated to ' . str_replace('_', ' ', $validated['repair_status']));
    }

    public function destroy(RepairJob $repairJob)
    {
        $repairJob->delete();
        return redirect()->route('repair-jobs.index');
    }
}
