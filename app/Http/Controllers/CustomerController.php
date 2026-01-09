<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount([
            'repairJobs as repairs_count' => function ($query) {
                $query->where('job_type', '!=', 'sale');
            },
            'repairJobs as sales_count' => function ($query) {
                $query->where('job_type', 'sale');
            }
        ])->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->get();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'type' => 'required|in:normal,shop',
        ]);

        Customer::create($validated);
        
        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($request->all());
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function ledger(Customer $customer)
    {
        $customer->load(['invoices.payments', 'invoices.repairJob']);
        
        $invoices = $customer->invoices()->latest()->get();
        // Calculate running balance or prepare stats if needed
        $totalDue = $customer->total_due;
        $totalPaid = $customer->invoices->sum('paid_amount');
        $totalBilled = $customer->invoices->sum('total_amount');

        return view('customers.ledger', compact('customer', 'invoices', 'totalDue', 'totalPaid', 'totalBilled'));
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
