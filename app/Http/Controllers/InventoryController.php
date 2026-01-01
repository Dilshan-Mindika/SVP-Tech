<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Part;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Part::latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $parts = $query->get();
        return view('inventory.index', compact('parts'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string',
            'model' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        Part::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Part added to inventory.');
    }

    public function edit($id)
    {
        $part = Part::findOrFail($id);
        return view('inventory.edit', compact('part'));
    }

    public function update(Request $request, $id)
    {
        $part = Part::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string',
            'model' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $part->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Part updated successfully.');
    }

    public function destroy($id)
    {
        $part = Part::findOrFail($id);
        $part->delete();
        return redirect()->route('inventory.index')->with('success', 'Part deleted from inventory.');
    }
}
