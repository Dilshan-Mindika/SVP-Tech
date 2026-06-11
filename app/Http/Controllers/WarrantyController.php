<?php

namespace App\Http\Controllers;

use App\Models\WarrantyClaim;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductSerial;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        $query = WarrantyClaim::with(['customer', 'product', 'invoice']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('claim_number', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $stats = [
            'total_count' => (clone $query)->count(),
            'pending_count' => (clone $query)->where('status', 'pending')->count(),
            'approved_count' => (clone $query)->whereIn('status', ['approved', 'replaced'])->count(),
            'rejected_count' => (clone $query)->where('status', 'rejected')->count(),
        ];

        $claims = $query->latest()->paginate(10);
        return view('warranty.index', compact('claims', 'stats'));
    }

    public function create(Request $request)
    {
        $selectedInvoiceId = $request->input('invoice_id') ?? old('invoice_id');
        $selectedProductId = $request->input('product_id') ?? old('product_id');
        $selectedSerialNumber = $request->input('serial_number') ?? old('serial_number');

        $selectedInvoice = null;
        $invoiceItems = collect();
        $customerInvoices = collect();

        if ($selectedInvoiceId) {
            $selectedInvoice = Invoice::with(['customer', 'items.product'])->find($selectedInvoiceId);
            if ($selectedInvoice) {
                $invoiceItems = $selectedInvoice->items;
                $customerInvoices = Invoice::where('customer_id', $selectedInvoice->customer_id)->orderBy('invoice_number', 'desc')->get();
            }
        } elseif (old('customer_id')) {
            $customerInvoices = Invoice::where('customer_id', old('customer_id'))->orderBy('invoice_number', 'desc')->get();
        }

        $customers = Customer::orderBy('name', 'asc')->get();
        $products = Product::orderBy('name', 'asc')->get();
        $invoices = Invoice::orderBy('invoice_number', 'desc')->get();
        
        return view('warranty.create', compact('customers', 'products', 'invoices', 'selectedInvoice', 'selectedInvoiceId', 'selectedProductId', 'selectedSerialNumber', 'invoiceItems', 'customerInvoices'));
    }

    public function customerInvoicesJson(Customer $customer)
    {
        $invoices = Invoice::where('customer_id', $customer->id)
            ->orderBy('invoice_number', 'desc')
            ->get(['id', 'invoice_number', 'total', 'created_at']);

        return response()->json(
            $invoices->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'invoice_number' => $inv->invoice_number,
                    'total' => number_format($inv->total, 2),
                    'date' => \Carbon\Carbon::parse($inv->created_at)->format('Y-m-d'),
                ];
            })
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'serial_number' => 'nullable|string|max:255',
            'invoice_id' => 'required|exists:invoices,id',
            'claim_date' => 'required|date',
            'issue_description' => 'required|string',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        // 1. Verify customer matches the invoice
        if ($invoice->customer_id != $request->customer_id) {
            return back()->withErrors(['invoice_id' => 'The selected invoice does not belong to the selected customer.'])->withInput();
        }

        // 2. Verify product was sold in the invoice
        $invoiceItem = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('product_id', $request->product_id)
            ->first();
        if (!$invoiceItem) {
            return back()->withErrors(['product_id' => 'The selected product was not purchased in this invoice.'])->withInput();
        }

        // 3. Verify serial number if provided matches the serial number sold on the invoice item
        if ($request->filled('serial_number')) {
            $hasSerial = InvoiceItem::where('invoice_id', $invoice->id)
                ->where('product_id', $request->product_id)
                ->where('serial_number', $request->serial_number)
                ->exists();
            if (!$hasSerial) {
                return back()->withErrors(['serial_number' => 'The specified serial number does not match this product in this invoice.'])->withInput();
            }
        }

        // 4. Verify claim date is within the warranty duration from the invoice date
        $product = Product::findOrFail($request->product_id);
        $warrantyMonths = $product->warranty_months;

        if ($invoiceItem && !empty($invoiceItem->warranty)) {
            $warrantyStr = strtolower($invoiceItem->warranty);
            if (preg_match('/(\d+)\s*month/', $warrantyStr, $matches)) {
                $warrantyMonths = intval($matches[1]);
            } elseif (preg_match('/(\d+)\s*year/', $warrantyStr, $matches)) {
                $warrantyMonths = intval($matches[1]) * 12;
            } elseif (strpos($warrantyStr, 'no warranty') !== false || strpos($warrantyStr, 'none') !== false) {
                $warrantyMonths = 0;
            }
        }

        if ($warrantyMonths === 0) {
            return back()->withErrors(['claim_date' => 'The selected product does not have any warranty.'])->withInput();
        }

        $invoiceDate = Carbon::parse($invoice->created_at);
        $claimDate = Carbon::parse($request->claim_date);
        
        $expiryDate = $invoiceDate->copy()->addMonths($warrantyMonths);
        
        if ($claimDate->greaterThan($expiryDate)) {
            return back()->withErrors(['claim_date' => "The warranty for this product expired on {$expiryDate->toDateString()} (Warranty period: {$warrantyMonths} months)."])->withInput();
        }

        if ($claimDate->lessThan($invoiceDate->startOfDay())) {
            return back()->withErrors(['claim_date' => 'The claim date cannot be before the invoice date.'])->withInput();
        }

        $lastClaim = WarrantyClaim::latest('id')->first();
        $nextId = $lastClaim ? $lastClaim->id + 1 : 1;
        $claimNo = 'WC-' . date('Ym') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $claim = WarrantyClaim::create([
            'claim_number' => $claimNo,
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'serial_number' => $request->serial_number,
            'invoice_id' => $request->invoice_id,
            'claim_date' => $request->claim_date,
            'issue_description' => $request->issue_description,
            'status' => 'pending',
        ]);

        // Optional: Update Serial Number status in stock if applicable
        if ($request->serial_number) {
            $serial = ProductSerial::where('serial_number', $request->serial_number)->first();
            if ($serial) {
                $serial->status = 'under_repair'; // marked as in warranty claim loop
                $serial->save();
            }
        }

        return redirect()->route('warranty.index')->with('success', "Warranty claim {$claimNo} registered.");
    }

    public function show(WarrantyClaim $claim)
    {
        $claim->load(['customer', 'product', 'invoice']);
        return view('warranty.show', compact('claim'));
    }

    public function updateStatus(Request $request, WarrantyClaim $claim)
    {
        $request->validate([
            'status' => 'required|string|in:pending,in_review,sent_to_supplier,replaced,returned_to_customer,rejected',
            'action_taken' => 'nullable|string',
        ]);

        $claim->status = $request->status;
        $claim->action_taken = $request->action_taken;

        if (in_array($request->status, ['replaced', 'returned_to_customer', 'rejected'])) {
            $claim->closed_date = Carbon::now()->toDateString();
            
            // If replaced, we might change the serial status back or handle replacement serial
            if ($claim->serial_number) {
                $serial = ProductSerial::where('serial_number', $claim->serial_number)->first();
                if ($serial) {
                    if ($request->status === 'replaced') {
                        $serial->status = 'returned'; // original returned to supplier / out of circulation
                    } else {
                        $serial->status = 'sold'; // returned to customer
                    }
                    $serial->save();
                }
            }
        }

        $claim->save();

        return redirect()->back()->with('success', "Warranty claim status updated.");
    }

    public function edit(WarrantyClaim $claim)
    {
        $customers = Customer::orderBy('name', 'asc')->get();
        $products = Product::orderBy('name', 'asc')->get();
        $invoices = Invoice::orderBy('invoice_number', 'desc')->get();
        return view('warranty.edit', compact('claim', 'customers', 'products', 'invoices'));
    }

    public function update(Request $request, WarrantyClaim $claim)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'serial_number' => 'nullable|string|max:255',
            'invoice_id' => 'required|exists:invoices,id',
            'claim_date' => 'required|date',
            'issue_description' => 'required|string',
            'status' => 'required|string|in:pending,in_review,sent_to_supplier,replaced,returned_to_customer,rejected',
            'action_taken' => 'nullable|string',
        ]);

        $claim->update($request->all());

        return redirect()->route('warranty.show', $claim->id)->with('success', "Warranty claim updated successfully.");
    }

    public function destroy(WarrantyClaim $claim)
    {
        $claim->delete();
        return redirect()->route('warranty.index')->with('success', "Warranty claim deleted successfully.");
    }
}
