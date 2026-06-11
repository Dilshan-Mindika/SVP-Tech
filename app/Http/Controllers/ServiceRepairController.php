<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use App\Models\RepairItem;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\DateFilterable;

class ServiceRepairController extends Controller
{
    use DateFilterable;

    public function index(Request $request)
    {
        $query = Repair::with('technician');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('repair_job_no', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('device_model', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $this->applyDateFilter($query, $request);

        $stats = [
            'total_count' => (clone $query)->count(),
            'pending_count' => (clone $query)->where('status', 'pending')->count(),
            'in_progress_count' => (clone $query)->whereIn('status', ['assigned', 'in_progress'])->count(),
            'completed_collected' => (clone $query)->whereIn('status', ['completed', 'collected'])->count(),
        ];

        $repairs = $query->latest()->paginate(10);
        return view('service_repairs.index', compact('repairs', 'stats'));
    }

    public function create()
    {
        $technicians = Employee::where('designation', 'Technician')
            ->where('status', 'active')
            ->get();
        return view('service_repairs.create', compact('technicians'));
    }

    public function store(Request $request)
    {
        $rules = [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'device_model' => 'required|string|max:255',
            'device_serial' => 'nullable|string|max:255',
            'issue_description' => 'required|string',
            'estimate_cost' => 'required|numeric|min:0',
            'assigned_technician_id' => 'nullable|exists:employees,id',
            'notes' => 'nullable|string',
            'customer_whatsapp' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'customer_nic' => 'nullable|string|max:20',
            'customer_company' => 'nullable|string|max:255',
            'referred_by' => 'nullable|string|max:255',
            'device_brand' => 'nullable|string|max:255',
            'device_color' => 'nullable|string|max:255',
            'device_processor' => 'nullable|string|max:255',
            'device_storage' => 'nullable|string|max:255',
            'device_ram' => 'nullable|string|max:255',
            'device_display_size' => 'nullable|string|max:255',
            'device_battery' => 'nullable|string|max:255',
            'device_charger_watt' => 'nullable|string|max:255',
            'physical_condition' => 'nullable|array',
            'physical_condition_other' => 'nullable|string',
            'accessories_received' => 'nullable|array',
            'accessories_other' => 'nullable|string',
            'windows_password' => 'nullable|string|max:255',
            'bios_password' => 'nullable|string|max:255',
            'bitlocker_status' => 'nullable|string|in:ON,OFF',
            'data_backup_required' => 'nullable|boolean',
            'customer_accept_data_loss' => 'nullable|boolean',
            'technical_inspection' => 'nullable|array',
            'chip_level_repair_notes' => 'nullable|array',
            'board_model' => 'nullable|string|max:255',
            'freelancer_technician' => 'nullable|string|max:255',
            'sent_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'inspection_fee' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
            'collected_by' => 'nullable|string|max:255',
            'date_collected' => 'nullable|date',
            'remaining_balance_paid' => 'nullable|numeric|min:0',
        ];

        $validated = $request->validate($rules);

        $lastRepair = Repair::latest('id')->first();
        $nextId = $lastRepair ? $lastRepair->id + 1 : 1;
        $jobNo = 'NNR' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $data = array_merge($validated, [
            'repair_job_no' => $jobNo,
            'final_cost' => $request->estimate_cost, // default to estimate
            'status' => 'received',
        ]);

        $data['physical_condition'] = $request->input('physical_condition', []);
        $data['accessories_received'] = $request->input('accessories_received', []);
        $data['technical_inspection'] = $request->input('technical_inspection', []);
        $data['chip_level_repair_notes'] = $request->input('chip_level_repair_notes', []);
        $data['data_backup_required'] = $request->has('data_backup_required');
        $data['customer_accept_data_loss'] = $request->has('customer_accept_data_loss');

        $repair = Repair::create($data);

        return redirect()->route('service_repairs.show', $repair->id)->with('success', "Repair job {$jobNo} logged successfully.");
    }

    public function show(Repair $repair)
    {
        $repair->load(['technician', 'items.product']);
        // Load in-stock products that can be used as spare parts
        $spareParts = Product::where('stock', '>', 0)->get();
        return view('service_repairs.show', compact('repair', 'spareParts'));
    }

    public function edit(Repair $repair)
    {
        $technicians = Employee::where('designation', 'Technician')
            ->where('status', 'active')
            ->get();
        return view('service_repairs.edit', compact('repair', 'technicians'));
    }

    public function update(Request $request, Repair $repair)
    {
        $rules = [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'device_model' => 'required|string|max:255',
            'device_serial' => 'nullable|string|max:255',
            'issue_description' => 'required|string',
            'estimate_cost' => 'required|numeric|min:0',
            'final_cost' => 'required|numeric|min:0',
            'assigned_technician_id' => 'nullable|exists:employees,id',
            'status' => 'required|string|in:received,diagnosing,repairing,ready,delivered',
            'notes' => 'nullable|string',
            'customer_whatsapp' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'customer_nic' => 'nullable|string|max:20',
            'customer_company' => 'nullable|string|max:255',
            'referred_by' => 'nullable|string|max:255',
            'device_brand' => 'nullable|string|max:255',
            'device_color' => 'nullable|string|max:255',
            'device_processor' => 'nullable|string|max:255',
            'device_storage' => 'nullable|string|max:255',
            'device_ram' => 'nullable|string|max:255',
            'device_display_size' => 'nullable|string|max:255',
            'device_battery' => 'nullable|string|max:255',
            'device_charger_watt' => 'nullable|string|max:255',
            'physical_condition' => 'nullable|array',
            'physical_condition_other' => 'nullable|string',
            'accessories_received' => 'nullable|array',
            'accessories_other' => 'nullable|string',
            'windows_password' => 'nullable|string|max:255',
            'bios_password' => 'nullable|string|max:255',
            'bitlocker_status' => 'nullable|string|in:ON,OFF',
            'data_backup_required' => 'nullable|boolean',
            'customer_accept_data_loss' => 'nullable|boolean',
            'technical_inspection' => 'nullable|array',
            'chip_level_repair_notes' => 'nullable|array',
            'board_model' => 'nullable|string|max:255',
            'freelancer_technician' => 'nullable|string|max:255',
            'sent_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'inspection_fee' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
            'collected_by' => 'nullable|string|max:255',
            'date_collected' => 'nullable|date',
            'remaining_balance_paid' => 'nullable|numeric|min:0',
        ];

        $validated = $request->validate($rules);

        $data = $validated;
        $data['physical_condition'] = $request->input('physical_condition', []);
        $data['accessories_received'] = $request->input('accessories_received', []);
        $data['technical_inspection'] = $request->input('technical_inspection', []);
        $data['chip_level_repair_notes'] = $request->input('chip_level_repair_notes', []);
        $data['data_backup_required'] = $request->has('data_backup_required');
        $data['customer_accept_data_loss'] = $request->has('customer_accept_data_loss');

        $repair->update($data);

        // Automatically create Invoice when marked as completed (status = ready or delivered)
        if (in_array($repair->status, ['ready', 'delivered'])) {
            $this->createInvoiceFromRepair($repair);
        }

        return redirect()->route('service_repairs.show', $repair->id)->with('success', "Repair job #{$repair->repair_job_no} details updated.");
    }

    public function receipt(Repair $repair)
    {
        $repair->load(['technician', 'items.product']);
        return view('service_repairs.receipt', compact('repair'));
    }

    private function createInvoiceFromRepair(Repair $repair)
    {
        if ($repair->invoice) {
            return $repair->invoice;
        }

        return DB::transaction(function () use ($repair) {
            // Find or create customer
            $customer = Customer::where('phone', $repair->customer_phone)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $repair->customer_name,
                    'phone' => $repair->customer_phone,
                    'email' => $repair->customer_email,
                    'address' => $repair->customer_address,
                ]);
            }

            // Find or create "Repair Service" product
            $laborProduct = Product::where('sku', 'SRV-REPAIR')->first();
            if (!$laborProduct) {
                $category = Category::first();
                $laborProduct = Product::create([
                    'category_id' => $category ? $category->id : 1,
                    'name' => 'Laptop Repair Service Labor',
                    'brand' => 'CloudTech',
                    'sku' => 'SRV-REPAIR',
                    'buying_price' => 0.00,
                    'price' => 0.00,
                    'stock' => 999999,
                    'warranty_months' => 0,
                    'description' => 'Labor charges for laptop repair service.',
                ]);
            }

            // Calculations
            $laborCost = floatval($repair->estimate_cost);
            $partsCost = 0.00;
            foreach ($repair->items as $item) {
                $partsCost += floatval($item->price * $item->quantity);
            }

            $subtotal = $laborCost + $partsCost;
            $grandTotal = $subtotal; // No flat discount/tax by default on auto invoice

            // Generate Invoice Number
            $lastInvoice = Invoice::latest('id')->first();
            $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
            $invoiceNumber = 'INV-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            // Create Invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customer->id,
                'user_id' => auth()->id() ?: 1,
                'repair_id' => $repair->id,
                'title' => "Repair Service Job #{$repair->repair_job_no}",
                'sale_type' => 'Retail',
                'special_note' => "Automatically generated from completed repair job #{$repair->repair_job_no}.",
                'subtotal' => $subtotal,
                'tax' => 0.00,
                'discount' => 0.00,
                'total' => $grandTotal,
                'payment_method' => 'Cash',
                'is_paid' => false,
                'customer_paid' => floatval($repair->advance_payment), // Credit advance payment
                'balance' => floatval($repair->advance_payment) - $grandTotal,
                'status' => ($repair->advance_payment >= $grandTotal) ? 'paid' : (($repair->advance_payment > 0) ? 'partial' : 'unpaid'),
            ]);

            // Save Invoice Items
            // 1. Labor
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $laborProduct->id,
                'quantity' => 1,
                'unit_price' => $laborCost,
            ]);

            // 2. Spare Parts
            foreach ($repair->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                ]);
            }

            return $invoice;
        });
    }

    public function addParts(Request $request, Repair $repair)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->withErrors("Insufficient stock for {$product->name}. Only {$product->stock} available.");
        }

        DB::transaction(function () use ($repair, $product, $request) {
            // Create repair item (spare part)
            RepairItem::create([
                'repair_id' => $repair->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);

            // Deduct stock
            $product->decrement('stock', $request->quantity);

            // Recalculate final cost (estimate + used parts cost)
            $partsCost = RepairItem::where('repair_id', $repair->id)
                ->select(DB::raw('SUM(price * quantity) as total'))
                ->first()->total;

            $repair->final_cost = $repair->estimate_cost + $partsCost;
            $repair->save();
        });

        return redirect()->route('service_repairs.show', $repair->id)->with('success', "Spare part added to repair job.");
    }

    public function destroy(Repair $repair)
    {
        $repair->delete();
        return redirect()->route('service_repairs.index')->with('success', "Repair job #{$repair->repair_job_no} deleted successfully.");
    }
}
