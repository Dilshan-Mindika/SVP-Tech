<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Repair;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return view('search.results', [
                'query' => $query,
                'products' => collect(),
                'customers' => collect(),
                'invoices' => collect(),
                'quotations' => collect(),
                'repairs' => collect(),
                'suppliers' => collect(),
            ]);
        }

        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->orWhere('brand', 'like', "%{$query}%")
            ->limit(10)->get();

        $customers = Customer::where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)->get();

        $invoices = Invoice::where('invoice_number', 'like', "%{$query}%")
            ->orWhereHas('customer', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(10)->get();

        $quotations = Quotation::where('quotation_number', 'like', "%{$query}%")
            ->orWhere('customer_name', 'like', "%{$query}%")
            ->orWhere('customer_phone', 'like', "%{$query}%")
            ->limit(10)->get();

        $repairs = Repair::where('repair_job_no', 'like', "%{$query}%")
            ->orWhere('customer_name', 'like', "%{$query}%")
            ->orWhere('customer_phone', 'like', "%{$query}%")
            ->orWhere('device_model', 'like', "%{$query}%")
            ->limit(10)->get();

        $suppliers = Supplier::where('name', 'like', "%{$query}%")
            ->orWhere('company_name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(10)->get();

        return view('search.results', compact('query', 'products', 'customers', 'invoices', 'quotations', 'repairs', 'suppliers'));
    }
}
