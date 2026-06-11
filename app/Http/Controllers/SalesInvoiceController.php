<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use App\Models\Employee;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\DateFilterable;

class SalesInvoiceController extends Controller
{
    use DateFilterable;
    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'user']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by invoice type
        if ($request->has('type') && $request->type !== 'all') {
            if ($request->type === 'tax') {
                $query->where('is_tax_invoice', true);
            } elseif ($request->type === 'standard') {
                $query->where('is_tax_invoice', false);
            }
        }

        // Filter by payment status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // Apply Date Filter from Trait
        $this->applyDateFilter($query, $request);

        $stats = [
            'total_count' => (clone $query)->count(),
            'total_revenue' => (clone $query)->sum('total'),
            'fully_unpaid' => (clone $query)->where('status', 'unpaid')->count(),
            'unpaid_receivables' => (clone $query)->where('status', '!=', 'paid')->get()->sum(fn($i) => max(0, $i->total - $i->customer_paid)),
        ];

        $invoices = $query->latest()->paginate(10);
        return view('sales_invoices.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        // Load products with active serials
        $products = Product::with(['serials' => function($q) {
                $q->where('status', 'in_stock');
            }])->orderBy('name', 'asc')->get();

        $customers = Customer::orderBy('name', 'asc')->get();
        $employees = Employee::orderBy('name', 'asc')->get();
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('bank_name', 'asc')->get();

        return view('sales_invoices.create', compact('products', 'customers', 'employees', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|required_without:customer_id',
            'customer_mobile' => 'nullable|string|required_without:customer_id',
            'customer_address' => 'nullable|string',
            'date' => 'required|date',
            'title' => 'nullable|string',
            'sale_type' => 'required|string',
            'employee_id' => 'nullable|exists:employees,id',
            'special_note' => 'nullable|string',
            'due_date' => 'nullable|date',
            'is_tax_invoice' => 'boolean',
            'payment_method' => 'required|string',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'is_paid' => 'required|string',
            'customer_paid' => 'nullable|numeric|min:0',
            'global_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'global_discount_amount' => 'nullable|numeric|min:0',
            'service_charges' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.free_quantity' => 'nullable|integer|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.serial_number' => 'nullable|string',
            'items.*.warranty' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            // Resolve or create Customer if not selected
            $customerId = $request->customer_id;
            if (!$customerId && $request->customer_name && $request->customer_mobile) {
                $customer = Customer::where('phone', $request->customer_mobile)->first();
                if (!$customer) {
                    $customer = Customer::create([
                        'name' => $request->customer_name,
                        'phone' => $request->customer_mobile,
                        'address' => $request->customer_address,
                    ]);
                } else {
                    if (!$customer->address && $request->customer_address) {
                        $customer->update(['address' => $request->customer_address]);
                    }
                }
                $customerId = $customer->id;
            }

            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Validate stock
                $qtyNeeded = intval($item['quantity']) + intval($item['free_quantity'] ?? 0);
                if ($product->stock < $qtyNeeded) {
                    return back()->withErrors("Product {$product->name} is out of stock (Requested: {$qtyNeeded}, Available: {$product->stock}).")->withInput();
                }

                // Validate serial status if serial was chosen
                if (!empty($item['serial_number'])) {
                    $serialExists = ProductSerial::where('product_id', $product->id)
                        ->where('serial_number', $item['serial_number'])
                        ->where('status', 'in_stock')
                        ->exists();
                    if (!$serialExists) {
                        return back()->withErrors("Serial number {$item['serial_number']} for {$product->name} is not in stock or is invalid.")->withInput();
                    }
                }

                $qty = intval($item['quantity']);
                $freeQty = intval($item['free_quantity'] ?? 0);
                $unitPrice = floatval($item['unit_price']);
                $discAmt = floatval($item['discount_amount'] ?? 0.00);
                $discPercent = floatval($item['discount_percentage'] ?? 0.00);
                
                $itemTotal = ($unitPrice * $qty) - $discAmt;
                if ($itemTotal < 0) $itemTotal = 0;
                
                $subtotal += ($unitPrice * $qty);

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'free_quantity' => $freeQty,
                    'unit_price' => $unitPrice,
                    'discount_amount' => $discAmt,
                    'discount_percentage' => $discPercent,
                    'total' => $itemTotal,
                    'serial_number' => $item['serial_number'] ?? null,
                    'warranty' => $item['warranty'] ?? null,
                ];
            }

            // Calculations
            $globalDiscountFlat = floatval($request->input('global_discount_amount', 0));
            $globalDiscountPercent = floatval($request->input('global_discount_percentage', 0));
            $serviceCharges = floatval($request->input('service_charges', 0));

            // Sum item totals
            $rowTotalSum = 0;
            foreach ($itemsData as $itemD) {
                $rowTotalSum += $itemD['total'];
            }

            // If global flat discount is 0 but percent is present
            if ($globalDiscountFlat == 0 && $globalDiscountPercent > 0) {
                $globalDiscountFlat = $rowTotalSum * ($globalDiscountPercent / 100);
            }

            $taxRate = $request->has('is_tax_invoice') && $request->is_tax_invoice ? 0.15 : 0.00;
            $taxableAmount = max(0, $rowTotalSum - $globalDiscountFlat);
            $tax = $taxableAmount * $taxRate;
            $grandTotal = $taxableAmount + $tax + $serviceCharges;

            $isPaid = $request->input('is_paid') === 'Yes';
            $customerPaid = floatval($request->input('customer_paid', 0));
            $balance = $customerPaid - $grandTotal;

            // Generate Invoice Number
            $lastInvoice = Invoice::latest('id')->first();
            $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
            $invoiceNumber = 'INV-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            // Create Invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'user_id' => Auth::id(),
                'employee_id' => $request->employee_id,
                'bank_account_id' => ($request->payment_method === 'Bank Transfer' || $request->payment_method === 'Bank') ? $request->bank_account_id : null,
                'title' => $request->title,
                'sale_type' => $request->sale_type,
                'special_note' => $request->special_note,
                'due_date' => $request->due_date,
                'is_tax_invoice' => $request->has('is_tax_invoice'),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $globalDiscountFlat,
                'global_discount_percentage' => $globalDiscountPercent,
                'global_discount_amount' => $globalDiscountFlat,
                'service_charges' => $serviceCharges,
                'total' => $grandTotal,
                'payment_method' => $request->payment_method,
                'is_paid' => $isPaid,
                'customer_paid' => $customerPaid,
                'balance' => $balance,
                'status' => in_array($request->payment_method, ['Koko', 'Payzy']) 
                    ? 'installment' 
                    : (($customerPaid >= $grandTotal) ? 'paid' : (($customerPaid > 0) ? 'partial' : 'unpaid')),
            ]);

            // Save Items & Update Stock/Serials
            foreach ($itemsData as $data) {
                $data['invoice_id'] = $invoice->id;
                InvoiceItem::create($data);

                // Update product stock
                $product = Product::find($data['product_id']);
                $qtyToDeduct = $data['quantity'] + $data['free_quantity'];
                $product->decrement('stock', $qtyToDeduct);

                // Update serial status if serial was chosen
                if (!empty($data['serial_number'])) {
                    $serial = ProductSerial::where('product_id', $product->id)
                        ->where('serial_number', $data['serial_number'])
                        ->first();
                    if ($serial) {
                        $serial->status = 'sold';
                        $serial->save();
                    }
                }
            }

            // Award Loyalty Points
            if ($customerId) {
                $pointsEarned = floor($grandTotal / 100);
                if ($pointsEarned > 0) {
                    $customer = Customer::find($customerId);
                    $customer->increment('loyalty_points', $pointsEarned);

                    LoyaltyTransaction::create([
                        'customer_id' => $customerId,
                        'points' => $pointsEarned,
                        'transaction_type' => 'earned',
                        'description' => "Earned on invoice {$invoiceNumber}",
                    ]);
                }
            }

            return redirect()->route('sales_invoices.show', $invoice->id)->with('success', "Invoice {$invoiceNumber} created successfully.");
        });
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'user', 'employee', 'items.product']);
        return view('sales_invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['customer', 'user', 'employee', 'items.product']);
        return view('sales_invoices.print', compact('invoice'));
    }

    public function itemsJson(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product']);
        return response()->json([
            'customer_id' => $invoice->customer_id,
            'items' => $invoice->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'serial_number' => $item->serial_number,
                    'warranty' => $item->warranty,
                ];
            })
        ]);
    }

    public function edit(Invoice $invoice)
    {
        $products = Product::with(['serials' => function($q) {
                $q->where('status', 'in_stock');
            }])->orderBy('name', 'asc')->get();
        $customers = Customer::orderBy('name', 'asc')->get();
        $employees = Employee::orderBy('name', 'asc')->get();
        $bankAccounts = BankAccount::where('is_active', true)->orderBy('bank_name', 'asc')->get();
        return view('sales_invoices.edit', compact('invoice', 'products', 'customers', 'employees', 'bankAccounts'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'title' => 'nullable|string',
            'sale_type' => 'required|string',
            'employee_id' => 'nullable|exists:employees,id',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'special_note' => 'nullable|string',
            'due_date' => 'nullable|date',
            'payment_method' => 'required|string',
            'is_paid' => 'required',
            'customer_paid' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        if ($request->payment_method !== 'Bank Transfer' && $request->payment_method !== 'Bank') {
            $data['bank_account_id'] = null;
        }

        // Recalculate status based on edit values
        $total = floatval($request->input('total', $invoice->total));
        $customerPaid = floatval($request->input('customer_paid', $invoice->customer_paid));
        $paymentMethod = $request->input('payment_method', $invoice->payment_method);

        $data['status'] = in_array($paymentMethod, ['Koko', 'Payzy']) 
            ? 'installment' 
            : (($customerPaid >= $total) ? 'paid' : (($customerPaid > 0) ? 'partial' : 'unpaid'));

        $invoice->update($data);

        return redirect()->route('sales_invoices.show', $invoice->id)->with('success', "Invoice {$invoice->invoice_number} updated successfully.");
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('sales_invoices.index')->with('success', "Invoice {$invoice->invoice_number} deleted successfully.");
    }
}
