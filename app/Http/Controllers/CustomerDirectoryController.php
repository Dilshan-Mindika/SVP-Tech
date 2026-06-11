<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LoyaltyTransaction;
use Illuminate\Http\Request;

class CustomerDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $stats = [
            'total_count' => (clone $query)->count(),
            'new_this_month' => (clone $query)->whereMonth('created_at', \Carbon\Carbon::now()->month)->whereYear('created_at', \Carbon\Carbon::now()->year)->count(),
            'active_customers' => (clone $query)->has('invoices')->count(),
            'total_receivables' => Invoice::whereIn('status', ['unpaid', 'partial'])->get()->sum(fn($i) => max(0, $i->total - $i->customer_paid)),
        ];

        $customers = $query->latest()->paginate(10);
        return view('customer_directory.index', compact('customers', 'stats'));
    }

    public function create()
    {
        return view('customer_directory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($request->validated());

        return redirect()->route('customer_directory.show', $customer->id)->with('success', "Customer {$customer->name} registered successfully.");
    }

    public function show(Customer $customer)
    {
        $customer->load(['loyaltyTransactions' => function($q) {
            $q->latest()->take(50);
        }]);

        // Load invoices for this customer
        $invoices = Invoice::where('customer_id', $customer->id)->latest()->get();

        return view('customer_directory.show', compact('customer', 'invoices'));
    }

    public function edit(Customer $customer)
    {
        return view('customer_directory.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $customer->update($request->validated());

        return redirect()->route('customer_directory.show', $customer->id)->with('success', "Customer {$customer->name} updated successfully.");
    }

    public function destroy(Customer $customer)
    {
        // Nullify references first - invoices will set customer_id to null
        $customer->delete();
        return redirect()->route('customer_directory.index')->with('success', "Customer {$customer->name} removed from the directory.");
    }
}
