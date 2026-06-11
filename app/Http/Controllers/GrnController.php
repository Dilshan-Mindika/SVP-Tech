<?php

namespace App\Http\Controllers;

use App\Models\Grn;
use App\Models\GrnItem;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\DateFilterable;

class GrnController extends Controller
{
    use DateFilterable;

    public function index(Request $request)
    {
        $query = Grn::with(['supplier', 'receiver']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('grn_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%")
                          ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $this->applyDateFilter($query, $request, 'date_received');

        $stats = [
            'total_count' => (clone $query)->count(),
            'total_value' => (clone $query)->sum('total_amount'),
            'received_items' => \App\Models\GrnItem::whereIn('grn_id', (clone $query)->pluck('id'))->sum('quantity'),
            'unique_suppliers' => (clone $query)->pluck('supplier_id')->unique()->count(),
        ];

        $grns = $query->latest()->paginate(10);
        return view('grn.index', compact('grns', 'stats'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $products = Product::orderBy('name', 'asc')->get();
        $categories = Category::orderBy('name', 'asc')->get();
        return view('grn.create', compact('suppliers', 'products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date_received' => 'required|date',
            'grn_number' => 'nullable|string|unique:grns,grn_number',
            'notes' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'service_charges' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_type' => 'required|string',
            'is_paid' => 'required|string',
            'paid_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.free_quantity' => 'nullable|integer|min:0',
            'items.*.buying_price' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.wholesale_price' => 'required|numeric|min:0',
            'items.*.barcode' => 'nullable|string|max:255',
            'items.*.expire_date' => 'nullable|date',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.single_discount_amount' => 'nullable|numeric|min:0',
            'items.*.warranty_months' => 'nullable|integer|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            // Generate GRN Number if not provided
            $grnNumber = $request->grn_number;
            if (empty($grnNumber)) {
                $lastGrn = Grn::latest('id')->first();
                $nextId = $lastGrn ? $lastGrn->id + 1 : 1;
                $grnNumber = 'GRN-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }

            $grn = Grn::create([
                'grn_number' => $grnNumber,
                'supplier_id' => $request->supplier_id,
                'received_by' => Auth::id(),
                'date_received' => $request->date_received,
                'subtotal' => $request->subtotal,
                'discount_percentage' => $request->discount_percentage ?: 0.00,
                'discount_amount' => $request->discount_amount ?: 0.00,
                'service_charges' => $request->service_charges ?: 0.00,
                'total_amount' => $request->total_amount,
                'payment_type' => $request->payment_type,
                'is_paid' => $request->is_paid === 'Yes',
                'paid_amount' => $request->paid_amount ?: 0.00,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $productId = $item['product_id'];
                $quantity = intval($item['quantity']);
                $freeQuantity = intval($item['free_quantity'] ?? 0);
                $totalUnits = $quantity + $freeQuantity;

                // Create GrnItem row
                GrnItem::create([
                    'grn_id' => $grn->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'free_quantity' => $freeQuantity,
                    'buying_price' => $item['buying_price'],
                    'wholesale_price' => $item['wholesale_price'],
                    'barcode' => $item['barcode'] ?: null,
                    'expire_date' => $item['expire_date'] ?: null,
                    'discount_percentage' => $item['discount_percentage'] ?: 0.00,
                    'discount_amount' => $item['discount_amount'] ?: 0.00,
                    'single_discount_amount' => $item['single_discount_amount'] ?: 0.00,
                    'warranty_months' => $item['warranty_months'] ?: 0,
                ]);

                // Update product catalog parameters
                $product = Product::find($productId);
                
                // Get starting count for serial numbers
                $currentSerialCount = ProductSerial::where('product_id', $product->id)->count();

                $product->increment('stock', $totalUnits);

                $productData = [
                    'buying_price' => $item['buying_price'],
                    'price' => $item['price'],
                    'wholesale_price' => $item['wholesale_price'],
                ];

                if (!empty($item['barcode'])) {
                    $productData['barcode'] = $item['barcode'];
                }
                if (!empty($item['expire_date'])) {
                    $productData['expire_date'] = $item['expire_date'];
                }
                if (isset($item['warranty_months']) && $item['warranty_months'] !== '') {
                    $productData['warranty_months'] = $item['warranty_months'];
                }

                $product->update($productData);

                // Generate new serial numbers for both accepted & free stock units
                for ($i = 1; $i <= $totalUnits; $i++) {
                    $serialNum = $currentSerialCount + $i;
                    ProductSerial::create([
                        'product_id' => $product->id,
                        'serial_number' => $product->sku . '-' . str_pad($serialNum, 4, '0', STR_PAD_LEFT),
                        'status' => 'in_stock',
                    ]);
                }
            }

            return redirect()->route('grn.index')->with('success', "GRN {$grnNumber} processed. Inventory restocked.");
        });
    }

    public function show(Grn $grn)
    {
        $grn->load(['supplier', 'receiver', 'items.product']);
        return view('grn.show', compact('grn'));
    }

    public function edit(Grn $grn)
    {
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $products = Product::orderBy('name', 'asc')->get();
        return view('grn.edit', compact('grn', 'suppliers', 'products'));
    }

    public function update(Request $request, Grn $grn)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date_received' => 'required|date',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $grn->update($request->only(['supplier_id', 'date_received', 'notes', 'total_amount']));

        return redirect()->route('grn.index')->with('success', "GRN {$grn->grn_number} updated successfully.");
    }

    public function destroy(Grn $grn)
    {
        $grn->delete();
        return redirect()->route('grn.index')->with('success', "GRN {$grn->grn_number} deleted successfully.");
    }
}
