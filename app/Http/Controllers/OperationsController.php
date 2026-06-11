<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ProductReturn;
use App\Models\ProductReturnItem;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Invoice;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Traits\DateFilterable;

class OperationsController extends Controller
{
    use DateFilterable;

    // Expenses
    public function expensesIndex(Request $request)
    {
        $query = Expense::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('expense_no', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
        }

        // Apply Date Filter from Trait on date_incurred column
        $this->applyDateFilter($query, $request, 'date_incurred');

        $stats = [
            'total_count' => (clone $query)->count(),
            'total_amount' => (clone $query)->sum('amount'),
            'cash_amount' => (clone $query)->where('payment_method', 'Cash')->sum('amount'),
            'bank_amount' => (clone $query)->where('payment_method', 'Bank Transfer')->sum('amount'),
        ];

        $expenses = $query->latest()->paginate(10);
        return view('expenses.index', compact('expenses', 'stats'));
    }

    public function expenseCreate()
    {
        return view('expenses.create');
    }

    public function expenseStore(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date_incurred' => 'required|date',
            'payment_method' => 'required|string',
            'details' => 'nullable|string',
        ]);

        $lastExpense = Expense::latest('id')->first();
        $nextId = $lastExpense ? $lastExpense->id + 1 : 1;
        $expenseNo = 'EXP-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        Expense::create([
            'expense_no' => $expenseNo,
            'category' => $request->category,
            'amount' => $request->amount,
            'date_incurred' => $request->date_incurred,
            'payment_method' => $request->payment_method,
            'details' => $request->details,
        ]);

        return redirect()->route('expenses.index')->with('success', "Expense {$expenseNo} logged successfully.");
    }

    // Returns
    public function returnsIndex(Request $request)
    {
        $query = ProductReturn::with(['invoice', 'supplier']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('return_number', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
        }

        // Apply Date Filter from Trait on created_at column
        $this->applyDateFilter($query, $request);

        $stats = [
            'total_count' => (clone $query)->count(),
            'total_refunded' => (clone $query)->sum('refund_amount'),
            'customer_returns' => (clone $query)->where('type', 'customer_return')->count(),
            'supplier_returns' => (clone $query)->where('type', 'supplier_return')->count(),
        ];

        $returns = $query->latest()->paginate(10);
        return view('returns.index', compact('returns', 'stats'));
    }

    public function returnCreate()
    {
        $invoices = Invoice::orderBy('invoice_number', 'desc')->get();
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $products = Product::orderBy('name', 'asc')->get();
        return view('returns.create', compact('invoices', 'suppliers', 'products'));
    }

    public function returnStore(Request $request)
    {
        $request->validate([
            'type' => 'required|in:customer_return,supplier_return',
            'invoice_id' => 'required_if:type,customer_return|nullable|exists:invoices,id',
            'supplier_id' => 'required_if:type,supplier_return|nullable|exists:suppliers,id',
            'reason' => 'required|string',
            'refund_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.serial_number' => 'nullable|string',
        ]);

        $productIds = collect($request->items)->pluck('product_id')->unique();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        if ($request->type === 'supplier_return') {
            $requestedQuantities = [];
            foreach ($request->items as $item) {
                $pid = $item['product_id'];
                $requestedQuantities[$pid] = ($requestedQuantities[$pid] ?? 0) + $item['quantity'];
            }

            foreach ($requestedQuantities as $pid => $totalQty) {
                $product = $products->get($pid);
                if (!$product || $product->stock < $totalQty) {
                    $name = $product ? $product->name : 'Product';
                    $stock = $product ? $product->stock : 0;
                    return redirect()->back()->withErrors(
                        "Cannot process supplier return: insufficient stock for {$name}. Available: {$stock}, Requested: {$totalQty}."
                    )->withInput();
                }
            }
        }

        return DB::transaction(function () use ($request, $products) {
            $lastReturn = ProductReturn::latest('id')->first();
            $nextId = $lastReturn ? $lastReturn->id + 1 : 1;
            $returnNo = 'RET-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $return = ProductReturn::create([
                'return_number' => $returnNo,
                'invoice_id' => $request->invoice_id,
                'supplier_id' => $request->supplier_id,
                'type' => $request->type,
                'reason' => $request->reason,
                'refund_amount' => $request->refund_amount,
                'status' => 'completed',
            ]);

            foreach ($request->items as $item) {
                $product = $products->get($item['product_id']);

                ProductReturnItem::create([
                    'return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);

                if ($request->type === 'customer_return') {
                    // Item returned by customer: restock the product
                    $product->increment('stock', $item['quantity']);

                    // Update serial status to in_stock
                    if (!empty($item['serial_number'])) {
                        $serial = ProductSerial::where('product_id', $product->id)
                            ->where('serial_number', $item['serial_number'])
                            ->first();
                        if ($serial) {
                            $serial->status = 'in_stock';
                            $serial->save();
                        }
                    }
                } else {
                    // Item returned to supplier: deduct from inventory
                    $product->decrement('stock', $item['quantity']);

                    // Update serial status to returned (out of stock)
                    if (!empty($item['serial_number'])) {
                        $serial = ProductSerial::where('product_id', $product->id)
                            ->where('serial_number', $item['serial_number'])
                            ->first();
                        if ($serial) {
                            $serial->status = 'returned';
                            $serial->save();
                        }
                    }
                }
            }

            return redirect()->route('returns.index')->with('success', "Return record {$returnNo} saved and inventory adjusted.");
        });
    }

    public function returnShow(ProductReturn $return)
    {
        $return->load(['invoice', 'supplier', 'items.product']);
        return view('returns.show', compact('return'));
    }

    // Expense Edit/Update/Delete
    public function expenseEdit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function expenseUpdate(Request $request, Expense $expense)
    {
        $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date_incurred' => 'required|date',
            'payment_method' => 'required|string',
            'details' => 'nullable|string',
        ]);

        $expense->update($request->all());

        return redirect()->route('expenses.index')->with('success', "Expense {$expense->expense_no} updated successfully.");
    }

    public function expenseDestroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', "Expense {$expense->expense_no} deleted successfully.");
    }

    // Return Edit/Update/Delete
    public function returnEdit(ProductReturn $return)
    {
        $invoices = Invoice::orderBy('invoice_number', 'desc')->get();
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $products = Product::orderBy('name', 'asc')->get();
        return view('returns.edit', compact('return', 'invoices', 'suppliers', 'products'));
    }

    public function returnUpdate(Request $request, ProductReturn $return)
    {
        $request->validate([
            'type' => 'required|in:customer_return,supplier_return',
            'invoice_id' => 'required_if:type,customer_return|nullable|exists:invoices,id',
            'supplier_id' => 'required_if:type,supplier_return|nullable|exists:suppliers,id',
            'reason' => 'required|string',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        $return->update($request->only(['type', 'invoice_id', 'supplier_id', 'reason', 'refund_amount']));

        return redirect()->route('returns.index')->with('success', "Return record {$return->return_number} updated successfully.");
    }

    public function returnDestroy(ProductReturn $return)
    {
        $return->delete();
        return redirect()->route('returns.index')->with('success', "Return record {$return->return_number} deleted successfully.");
    }
}
