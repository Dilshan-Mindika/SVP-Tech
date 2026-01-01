<?php

namespace App\Http\Controllers;

use App\Models\RepairJob;
use Illuminate\Http\Request;

class RepairJobController extends Controller
{
    public function index()
    {
        $jobs = RepairJob::with(['customer', 'technician'])->latest()->get();
        return view('repair_jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('repair_jobs.create');
    }

    public function store(Request $request)
    {
        // 1. Determine Validation Rules
        $rules = [
            'customer_type' => 'required|in:existing,new',
            'laptop_brand' => 'required|string|max:255',
            'laptop_model' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'fault_description' => 'required|string',
            'repair_notes' => 'nullable|string',
        ];

        // Conditional Validation
        if ($request->customer_type === 'existing') {
            $rules['customer_id'] = 'required|exists:customers,id';
        } else {
            $rules['new_customer_name'] = 'required|string|max:255';
            $rules['new_customer_phone'] = 'required|string|max:20';
            $rules['new_customer_email'] = 'nullable|email';
            $rules['new_customer_address'] = 'nullable|string|max:500';
        }

        $validated = $request->validate($rules);

        // 2. Handle Customer (Get existing ID or Create New)
        if ($request->customer_type === 'new') {
            // Create New Customer
            $customer = \App\Models\Customer::create([
                'name' => $validated['new_customer_name'],
                'phone' => $validated['new_customer_phone'],
                'email' => $validated['new_customer_email'],
                'address' => $validated['new_customer_address'],
            ]);
            $customerId = $customer->id;
        } else {
            $customerId = $validated['customer_id'];
        }

        // 3. Create Job
        $job = RepairJob::create([
            'customer_id' => $customerId,
            'technician_id' => null,
            'laptop_brand' => $validated['laptop_brand'],
            'laptop_model' => $validated['laptop_model'],
            'serial_number' => $validated['serial_number'],
            'fault_description' => $validated['fault_description'],
            'repair_notes' => $validated['repair_notes'],
            'repair_status' => 'pending',
            'status' => 'pending',
        ]);

        return redirect()->route('repair-jobs.index')->with('success', 'Repair Job created successfully for customer.');
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
            'technician_id' => 'nullable|exists:technicians,id',
            'fault_description' => 'required|string',
            'repair_notes' => 'nullable|string',
            'parts_used_cost' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'final_price' => 'nullable|numeric|min:0',
        ]);

        $repairJob->update([
            'repair_status' => $validated['repair_status'],
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
