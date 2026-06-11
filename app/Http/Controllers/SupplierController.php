<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        $stats = [
            'total_count' => (clone $query)->count(),
            'total_deliveries' => \App\Models\Grn::whereIn('supplier_id', (clone $query)->pluck('id'))->count(),
            'supplied_value' => \App\Models\Grn::whereIn('supplier_id', (clone $query)->pluck('id'))->sum('total_amount'),
            'active_suppliers' => \App\Models\Grn::whereMonth('date_received', \Carbon\Carbon::now()->month)->pluck('supplier_id')->unique()->count(),
        ];

        $suppliers = $query->latest()->paginate(10);
        return view('suppliers.index', compact('suppliers', 'stats'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
        ]);

        Supplier::create($request->validated());

        return redirect()->route('suppliers.index')->with('success', "Supplier {$request->name} added successfully.");
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
        ]);

        $supplier->update($request->validated());

        return redirect()->route('suppliers.index')->with('success', "Supplier {$request->name} updated successfully.");
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', "Supplier {$supplier->name} deleted successfully.");
    }
}
