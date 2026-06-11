<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ProductSerial;
use App\Models\LoyaltyTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Traits\DateFilterable;

class QuotationController extends Controller
{
    use DateFilterable;

    public function index(Request $request)
    {
        $query = Quotation::with('customer');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('quotation_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
        }

        // Apply Date Filter from Trait
        $this->applyDateFilter($query, $request);

        $stats = [
            'total_count' => (clone $query)->count(),
            'total_value' => (clone $query)->sum('total'),
            'pending_count' => (clone $query)->where('status', 'pending')->count(),
            'converted_count' => (clone $query)->where('status', 'converted')->count(),
        ];

        $quotations = $query->latest()->paginate(10);
        return view('quotations.index', compact('quotations', 'stats'));
    }

    public function create()
    {
        $products = Product::all();
        $customers = Customer::orderBy('name', 'asc')->get();
        return view('quotations.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_without:customer_id|nullable|string',
            'customer_phone' => 'required_without:customer_id|nullable|string',
            'valid_days' => 'required|integer|min:1',
            'is_tax_quotation' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $customerName = '';
            $customerPhone = '';
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                $customerName = $customer->name;
                $customerPhone = $customer->phone;
            } else {
                $customerName = $request->customer_name;
                $customerPhone = $request->customer_phone;
            }

            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal += ($product->price * $item['quantity']);

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];
            }

            $taxRate = $request->has('is_tax_quotation') ? 0.15 : 0.00;
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;

            // Generate Quotation Number
            $lastQuote = Quotation::latest('id')->first();
            $nextId = $lastQuote ? $lastQuote->id + 1 : 1;
            $quotationNumber = 'QT-' . Carbon::now()->format('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'quotation_number' => $quotationNumber,
                'customer_id' => $request->customer_id,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'valid_until' => Carbon::now()->addDays($request->valid_days),
                'status' => 'sent',
                'notes' => $request->notes,
            ]);

            foreach ($itemsData as $data) {
                $data['quotation_id'] = $quotation->id;
                QuotationItem::create($data);
            }

            return redirect()->route('quotations.show', $quotation->id)->with('success', "Quotation {$quotationNumber} generated successfully.");
        });
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'items.product']);
        return view('quotations.show', compact('quotation'));
    }

    public function print(Quotation $quotation)
    {
        $quotation->load(['customer', 'items.product']);
        return view('quotations.print', compact('quotation'));
    }

    public function convertToInvoice(Quotation $quotation)
    {
        if ($quotation->status === 'accepted') {
            return back()->withErrors('This quotation has already been converted to an invoice.');
        }

        if ($quotation->status === 'expired') {
            return back()->withErrors('This quotation has expired and cannot be converted.');
        }

        if (\Carbon\Carbon::parse($quotation->valid_until)->isPast()) {
            return back()->withErrors('This quotation has passed its validity date (' . \Carbon\Carbon::parse($quotation->valid_until)->format('d M Y') . ') and cannot be converted.');
        }

        $quotation->load('items.product');

        // Verify stock for conversion
        foreach ($quotation->items as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->withErrors("Cannot convert: Product {$item->product->name} does not have enough stock ({$item->product->stock} available).");
            }
        }

        return DB::transaction(function () use ($quotation) {
            // Generate Invoice Number
            $lastInvoice = Invoice::latest('id')->first();
            $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
            $invoiceNumber = 'INV-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            // If quotation doesn't have customer_id (guest quote), check or create guest customer or handle
            $customerId = $quotation->customer_id;
            if (!$customerId) {
                // Find or create customer by phone
                $customer = Customer::firstOrCreate(
                    ['phone' => $quotation->customer_phone],
                    ['name' => $quotation->customer_name]
                );
                $customerId = $customer->id;
            }

            // Create Invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customerId,
                'user_id' => Auth::id(),
                'is_tax_invoice' => $quotation->tax > 0,
                'subtotal' => $quotation->subtotal,
                'tax' => $quotation->tax,
                'discount' => 0.00,
                'total' => $quotation->total,
                'payment_method' => 'Cash', // Default
                'status' => 'paid',
            ]);

            // Save Items & Decrement Stock
            foreach ($quotation->items as $item) {
                // Grab the first available serial if product uses serials
                $serial = ProductSerial::where('product_id', $item->product_id)
                    ->where('status', 'in_stock')
                    ->first();

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'serial_number' => $serial ? $serial->serial_number : null,
                ]);

                // Update product stock
                $item->product->decrement('stock', $item->quantity);

                // Update serial status
                if ($serial) {
                    $serial->status = 'sold';
                    $serial->save();
                }
            }

            // Update Quotation Status
            $quotation->status = 'accepted';
            $quotation->save();

            // Award Loyalty Points
            $pointsEarned = floor($quotation->total / 100);
            if ($pointsEarned > 0) {
                $customer = Customer::find($customerId);
                $customer->increment('loyalty_points', $pointsEarned);

                LoyaltyTransaction::create([
                    'customer_id' => $customer->id,
                    'points' => $pointsEarned,
                    'transaction_type' => 'earned',
                    'description' => "Earned on converted invoice {$invoiceNumber}",
                ]);
            }

            return redirect()->route('invoices.show', $invoice->id)->with('success', "Quotation successfully converted to Invoice {$invoiceNumber}.");
        });
    }

    public function edit(Quotation $quotation)
    {
        $products = Product::all();
        $customers = Customer::orderBy('name', 'asc')->get();
        return view('quotations.edit', compact('quotation', 'products', 'customers'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_without:customer_id|nullable|string',
            'customer_phone' => 'required_without:customer_id|nullable|string',
            'valid_until' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required|string|in:sent,accepted,expired,cancelled',
            'notes' => 'nullable|string',
        ]);

        $quotation->update($request->all());

        return redirect()->route('quotations.show', $quotation->id)->with('success', "Quotation {$quotation->quotation_number} updated successfully.");
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', "Quotation {$quotation->quotation_number} deleted successfully.");
    }
}
