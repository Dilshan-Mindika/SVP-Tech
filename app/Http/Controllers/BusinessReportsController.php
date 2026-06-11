<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Grn;
use App\Models\ProductReturn;
use App\Models\Expense;
use App\Models\Attendance;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BusinessReportsController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->input('report_type', 'sales');
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::now()->endOfMonth()->toDateString());

        $data = $this->generateReportData($reportType, $fromDate, $toDate);
        $chartData = $this->generateChartData($reportType, $fromDate, $toDate);

        if ($request->has('download')) {
            return $this->downloadCsv($reportType, $data, $fromDate, $toDate);
        }

        if ($request->has('print')) {
            return view('business_reports.print', compact('reportType', 'data', 'fromDate', 'toDate'));
        }

        $stats = $this->calculateReportStats($reportType, $data);

        return view('business_reports.index', compact('reportType', 'data', 'fromDate', 'toDate', 'chartData', 'stats'));
    }

    private function calculateReportStats($reportType, $data)
    {
        switch ($reportType) {
            case 'sales':
                $collection = collect($data);
                return [
                    ['title' => 'Total Invoices', 'value' => $collection->count(), 'sub' => 'Generated slips', 'icon' => 'fa-file-invoice', 'color' => 'cyan'],
                    ['title' => 'Total Sales Volume', 'value' => 'Rs. ' . number_format($collection->sum('total'), 2), 'sub' => 'Gross earnings', 'icon' => 'fa-wallet', 'color' => 'emerald'],
                    ['title' => 'Total Discounts', 'value' => 'Rs. ' . number_format($collection->sum('discount'), 2), 'sub' => 'Discounts given', 'icon' => 'fa-tags', 'color' => 'rose'],
                    ['title' => 'Unpaid Invoices', 'value' => $collection->where('is_paid', false)->count(), 'sub' => 'Awaiting payment', 'icon' => 'fa-clock', 'color' => 'amber'],
                ];
            case 'profit':
                return [
                    ['title' => 'Total Revenue', 'value' => 'Rs. ' . number_format($data['total_revenue'], 2), 'sub' => 'Gross sales revenue', 'icon' => 'fa-coins', 'color' => 'cyan'],
                    ['title' => 'Total Cost', 'value' => 'Rs. ' . number_format($data['total_cost'], 2), 'sub' => 'Cost of goods sold', 'icon' => 'fa-truck-loading', 'color' => 'amber'],
                    ['title' => 'Overhead Expenses', 'value' => 'Rs. ' . number_format($data['total_expenses'], 2), 'sub' => 'Operating expenses', 'icon' => 'fa-file-invoice-dollar', 'color' => 'rose'],
                    ['title' => 'Net Profit', 'value' => 'Rs. ' . number_format($data['net_profit'], 2), 'sub' => 'Earnings after expenses', 'icon' => 'fa-sack-dollar', 'color' => 'emerald'],
                ];
            case 'stock':
                $prods = collect($data['products']);
                return [
                    ['title' => 'Total Products', 'value' => $prods->count(), 'sub' => 'Unique SKUs', 'icon' => 'fa-box', 'color' => 'cyan'],
                    ['title' => 'Total Stock Qty', 'value' => $prods->sum('stock'), 'sub' => 'Units in hand', 'icon' => 'fa-boxes-stacked', 'color' => 'slate'],
                    ['title' => 'Total Cost Value', 'value' => 'Rs. ' . number_format($data['total_cost_value'], 2), 'sub' => 'Buying valuation', 'icon' => 'fa-tags', 'color' => 'amber'],
                    ['title' => 'Total Selling Value', 'value' => 'Rs. ' . number_format($data['total_sale_value'], 2), 'sub' => 'Selling valuation', 'icon' => 'fa-circle-dollar-to-slot', 'color' => 'emerald'],
                ];
            case 'product':
                $collection = collect($data);
                return [
                    ['title' => 'Unique Products Sold', 'value' => $collection->count(), 'sub' => 'Distinct items', 'icon' => 'fa-boxes-packing', 'color' => 'cyan'],
                    ['title' => 'Total Quantity Sold', 'value' => $collection->sum('qty_sold'), 'sub' => 'Units purchased', 'icon' => 'fa-cart-shopping', 'color' => 'emerald'],
                    ['title' => 'Total Free Given', 'value' => $collection->sum('free_qty'), 'sub' => 'FOC units', 'icon' => 'fa-gift', 'color' => 'rose'],
                    ['title' => 'Total Revenue', 'value' => 'Rs. ' . number_format($collection->sum('revenue'), 2), 'sub' => 'Revenue from products', 'icon' => 'fa-dollar-sign', 'color' => 'cyan'],
                ];
            case 'customer_payment':
                $collection = collect($data);
                $top = $collection->sortByDesc('total_volume')->first();
                return [
                    ['title' => 'Active Methods', 'value' => $collection->count(), 'sub' => 'Payment systems', 'icon' => 'fa-credit-card', 'color' => 'cyan'],
                    ['title' => 'Total Transactions', 'value' => $collection->sum('count'), 'sub' => 'Payments made', 'icon' => 'fa-list-check', 'color' => 'slate'],
                    ['title' => 'Total Paid Volume', 'value' => 'Rs. ' . number_format($collection->sum('total_volume'), 2), 'sub' => 'Received volume', 'icon' => 'fa-money-bill-transfer', 'color' => 'emerald'],
                    ['title' => 'Top Method', 'value' => $top ? $top->payment_method : 'N/A', 'sub' => 'By payment volume', 'icon' => 'fa-star', 'color' => 'amber'],
                ];
            case 'sales_ref':
                $collection = collect($data);
                $top = $collection->sortByDesc('total_volume')->first();
                return [
                    ['title' => 'Active Sales Reps', 'value' => $collection->count(), 'sub' => 'Staff active in sales', 'icon' => 'fa-users', 'color' => 'cyan'],
                    ['title' => 'Total Invoices', 'value' => $collection->sum('count'), 'sub' => 'Invoices written', 'icon' => 'fa-file-lines', 'color' => 'slate'],
                    ['title' => 'Total Rep Volume', 'value' => 'Rs. ' . number_format($collection->sum('total_volume'), 2), 'sub' => 'Staff-made volume', 'icon' => 'fa-wallet', 'color' => 'emerald'],
                    ['title' => 'Top Salesperson', 'value' => $top && $top->employee ? $top->employee->name : 'N/A', 'sub' => 'Highest sales amount', 'icon' => 'fa-award', 'color' => 'amber'],
                ];
            case 'customer_credit':
                $collection = collect($data);
                return [
                    ['title' => 'Debtors Count', 'value' => $collection->count(), 'sub' => 'Unpaid customers', 'icon' => 'fa-users-rectangle', 'color' => 'rose'],
                    ['title' => 'Total Outstanding', 'value' => 'Rs. ' . number_format(abs($collection->sum('balance')), 2), 'sub' => 'Pending collection', 'icon' => 'fa-hand-holding-dollar', 'color' => 'rose'],
                    ['title' => 'Total Sales Amount', 'value' => 'Rs. ' . number_format($collection->sum('total'), 2), 'sub' => 'Sum of credit invoices', 'icon' => 'fa-money-check-dollar', 'color' => 'cyan'],
                    ['title' => 'Average Debt', 'value' => $collection->count() > 0 ? 'Rs. ' . number_format(abs($collection->sum('balance')) / $collection->count(), 2) : 'Rs. 0.00', 'sub' => 'Mean outstanding per client', 'icon' => 'fa-calculator', 'color' => 'amber'],
                ];
            case 'supplier':
                $collection = collect($data);
                return [
                    ['title' => 'GRNs Received', 'value' => $collection->count(), 'sub' => 'Total GRN batches', 'icon' => 'fa-file-invoice', 'color' => 'cyan'],
                    ['title' => 'Unique Suppliers', 'value' => $collection->pluck('supplier_id')->unique()->count(), 'sub' => 'Active vendors', 'icon' => 'fa-building', 'color' => 'amber'],
                    ['title' => 'Total Received Value', 'value' => 'Rs. ' . number_format($collection->sum('total_amount'), 2), 'sub' => 'Purchasing amount', 'icon' => 'fa-truck-ramp-box', 'color' => 'emerald'],
                    ['title' => 'Pending Audits', 'value' => $collection->where('status', 'pending')->count(), 'sub' => 'Awaiting confirmation', 'icon' => 'fa-spinner', 'color' => 'rose'],
                ];
            case 'sale_type':
                $collection = collect($data);
                $top = $collection->sortByDesc('total_volume')->first();
                return [
                    ['title' => 'Sale Channels', 'value' => $collection->count(), 'sub' => 'Active channels', 'icon' => 'fa-store', 'color' => 'cyan'],
                    ['title' => 'Total Invoices', 'value' => $collection->sum('count'), 'sub' => 'Total volume', 'icon' => 'fa-file-invoice', 'color' => 'slate'],
                    ['title' => 'Total Channel Volume', 'value' => 'Rs. ' . number_format($collection->sum('total_volume'), 2), 'sub' => 'Overall sales', 'icon' => 'fa-money-bill-wave', 'color' => 'emerald'],
                    ['title' => 'Top Channel', 'value' => $top ? $top->sale_type : 'N/A', 'sub' => 'Primary sale source', 'icon' => 'fa-bolt', 'color' => 'amber'],
                ];
            case 'return':
                $collection = collect($data);
                return [
                    ['title' => 'Total Return Slips', 'value' => $collection->count(), 'sub' => 'Product returns logged', 'icon' => 'fa-right-left', 'color' => 'rose'],
                    ['title' => 'Total Refund Value', 'value' => 'Rs. ' . number_format($collection->sum('refund_amount'), 2), 'sub' => 'Refunded amount', 'icon' => 'fa-hand-holding-dollar', 'color' => 'rose'],
                    ['title' => 'Customer Returns', 'value' => $collection->where('type', 'customer_return')->count(), 'sub' => 'Restocked items', 'icon' => 'fa-user-tag', 'color' => 'cyan'],
                    ['title' => 'Supplier Returns', 'value' => $collection->where('type', 'supplier_return')->count(), 'sub' => 'Deducted inventory', 'icon' => 'fa-truck-field', 'color' => 'amber'],
                ];
            case 'expenses':
                $collection = collect($data);
                $top = $collection->sortByDesc('total_amount')->first();
                return [
                    ['title' => 'Unique Categories', 'value' => $collection->count(), 'sub' => 'Overhead items', 'icon' => 'fa-sitemap', 'color' => 'cyan'],
                    ['title' => 'Total Slips', 'value' => $collection->sum('count'), 'sub' => 'Receipts logged', 'icon' => 'fa-receipt', 'color' => 'slate'],
                    ['title' => 'Total Expense Amount', 'value' => 'Rs. ' . number_format($collection->sum('total_amount'), 2), 'sub' => 'Sum of overheads', 'icon' => 'fa-wallet', 'color' => 'rose'],
                    ['title' => 'Top Category', 'value' => $top ? $top->category : 'N/A', 'sub' => 'Highest expenditure', 'icon' => 'fa-chart-pie', 'color' => 'amber'],
                ];
            case 'attendance':
                $collection = collect($data);
                return [
                    ['title' => 'Staff Tracked', 'value' => $collection->count(), 'sub' => 'Active personnel', 'icon' => 'fa-users', 'color' => 'cyan'],
                    ['title' => 'Total Present Days', 'value' => $collection->sum('present_days'), 'sub' => 'In office / shop', 'icon' => 'fa-circle-check', 'color' => 'emerald'],
                    ['title' => 'Total Late Days', 'value' => $collection->sum('late_days'), 'sub' => 'Arrived tardy', 'icon' => 'fa-clock', 'color' => 'amber'],
                    ['title' => 'Total Absent Days', 'value' => $collection->sum('absent_days'), 'sub' => 'Leave or no-show', 'icon' => 'fa-circle-xmark', 'color' => 'rose'],
                ];
            case 'salary':
                $collection = collect($data);
                return [
                    ['title' => 'Slips Issued', 'value' => $collection->count(), 'sub' => 'Total payouts logged', 'icon' => 'fa-money-bill-wave', 'color' => 'cyan'],
                    ['title' => 'Total Disbursed', 'value' => 'Rs. ' . number_format($collection->sum('amount_paid'), 2), 'sub' => 'Gross payroll costs', 'icon' => 'fa-sack-dollar', 'color' => 'emerald'],
                    ['title' => 'Average Slip Payout', 'value' => $collection->count() > 0 ? 'Rs. ' . number_format($collection->sum('amount_paid') / $collection->count(), 2) : 'Rs. 0.00', 'sub' => 'Mean monthly pay', 'icon' => 'fa-calculator', 'color' => 'amber'],
                    ['title' => 'Staff Members Paid', 'value' => $collection->pluck('employee_id')->unique()->count(), 'sub' => 'Unique employees', 'icon' => 'fa-user-tie', 'color' => 'cyan'],
                ];
        }
        return [];
    }

    private function generateReportData($type, $from, $to)
    {
        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->endOfDay();

        switch ($type) {
            case 'sales':
                // Invoices summary
                return Invoice::whereBetween('created_at', [$start, $end])
                    ->with(['customer', 'user'])
                    ->latest()
                    ->get();

            case 'profit':
                // Revenue - Buying Cost - Expenses
                $salesData = InvoiceItem::whereHas('invoice', function($q) use ($start, $end) {
                        $q->whereBetween('created_at', [$start, $end]);
                    })
                    ->select('product_id', DB::raw('SUM(quantity) as qty_sold'), DB::raw('SUM(total) as revenue'))
                    ->groupBy('product_id')
                    ->with('product')
                    ->get();

                $expensesTotal = Expense::whereBetween('date_incurred', [$from, $to])->sum('amount');
                
                $totalRevenue = 0;
                $totalCost = 0;
                $itemsBreakdown = [];

                foreach ($salesData as $item) {
                    $buyingPrice = $item->product ? $item->product->buying_price : 0;
                    $cost = $buyingPrice * $item->qty_sold;
                    $revenue = $item->revenue;
                    $profit = $revenue - $cost;

                    $totalRevenue += $revenue;
                    $totalCost += $cost;

                    $itemsBreakdown[] = [
                        'product_name' => $item->product ? $item->product->name : 'Unknown Product',
                        'qty_sold' => $item->qty_sold,
                        'buying_price' => $buyingPrice,
                        'selling_price' => $item->product ? $item->product->price : 0,
                        'revenue' => $revenue,
                        'cost' => $cost,
                        'profit' => $profit
                    ];
                }

                $netProfit = $totalRevenue - $totalCost - $expensesTotal;

                return [
                    'breakdown' => $itemsBreakdown,
                    'total_revenue' => $totalRevenue,
                    'total_cost' => $totalCost,
                    'total_expenses' => $expensesTotal,
                    'net_profit' => $netProfit
                ];

            case 'stock':
                // Inventory status
                $products = Product::with('category')->get();
                $data = [];
                $totalCostValue = 0;
                $totalSaleValue = 0;

                foreach ($products as $p) {
                    $costVal = $p->buying_price * $p->stock;
                    $saleVal = $p->price * $p->stock;
                    $totalCostValue += $costVal;
                    $totalSaleValue += $saleVal;

                    $data[] = [
                        'name' => $p->name,
                        'sku' => $p->sku,
                        'category' => $p->category ? $p->category->name : 'N/A',
                        'stock' => $p->stock,
                        'buying_price' => $p->buying_price,
                        'sale_price' => $p->price,
                        'cost_value' => $costVal,
                        'sale_value' => $saleVal
                    ];
                }

                return [
                    'products' => $data,
                    'total_cost_value' => $totalCostValue,
                    'total_sale_value' => $totalSaleValue
                ];

            case 'product':
                // Product sales movements
                return InvoiceItem::whereHas('invoice', function($q) use ($start, $end) {
                        $q->whereBetween('created_at', [$start, $end]);
                    })
                    ->select('product_id', DB::raw('SUM(quantity) as qty_sold'), DB::raw('SUM(free_quantity) as free_qty'), DB::raw('SUM(total) as revenue'))
                    ->groupBy('product_id')
                    ->with('product')
                    ->orderBy('qty_sold', 'desc')
                    ->get();

            case 'customer_payment':
                // Invoices by payment method
                return Invoice::whereBetween('created_at', [$start, $end])
                    ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total_volume'))
                    ->groupBy('payment_method')
                    ->get();

            case 'sales_ref':
                // Salesperson sales performance
                return Invoice::whereBetween('created_at', [$start, $end])
                    ->whereNotNull('employee_id')
                    ->select('employee_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total_volume'))
                    ->groupBy('employee_id')
                    ->with('employee')
                    ->orderBy('total_volume', 'desc')
                    ->get();

            case 'customer_credit':
                // Unpaid/Partially paid Invoices
                return Invoice::whereBetween('created_at', [$start, $end])
                    ->where(function($q) {
                        $q->where('is_paid', false)
                          ->orWhere('balance', '<', 0);
                    })
                    ->with('customer')
                    ->get();

            case 'supplier':
                // GRNs and Suppliers additions
                return Grn::whereBetween('date_received', [$from, $to])
                    ->with(['supplier', 'receivedBy'])
                    ->latest()
                    ->get();

            case 'sale_type':
                // Invoices by Sale Type (Shop, Online, etc.)
                return Invoice::whereBetween('created_at', [$start, $end])
                    ->select('sale_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total_volume'))
                    ->groupBy('sale_type')
                    ->get();

            case 'return':
                // Product Returns
                return ProductReturn::whereBetween('created_at', [$start, $end])
                    ->with(['invoice', 'supplier'])
                    ->latest()
                    ->get();

            case 'expenses':
                // Expenses summary
                return Expense::whereBetween('date_incurred', [$from, $to])
                    ->select('category', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
                    ->groupBy('category')
                    ->get();

            case 'attendance':
                // Employee attendance summary
                return Attendance::whereBetween('date', [$from, $to])
                    ->select('employee_id', 
                        DB::raw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days"),
                        DB::raw("SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_days"),
                        DB::raw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days")
                    )
                    ->groupBy('employee_id')
                    ->with('employee')
                    ->get();

            case 'salary':
                // Salary payroll report
                return Salary::whereBetween('payment_date', [$from, $to])
                    ->with('employee')
                    ->latest()
                    ->get();
        }

        return [];
    }

    private function generateChartData($type, $from, $to)
    {
        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->endOfDay();
        $daysDiff = $start->diffInDays($end);

        // Determine date grouping format
        $groupFormat = 'Y-m-d'; // Daily
        $labelFormat = 'M d';
        if ($daysDiff > 60 && $daysDiff <= 730) {
            $groupFormat = 'Y-m'; // Monthly
            $labelFormat = 'M Y';
        } elseif ($daysDiff > 730) {
            $groupFormat = 'Y'; // Annual
            $labelFormat = 'Y';
        }

        switch ($type) {
            case 'sales':
                // Group sales over time
                $invoices = Invoice::whereBetween('created_at', [$start, $end])
                    ->orderBy('created_at')
                    ->get();
                
                $grouped = $invoices->groupBy(function($item) use ($groupFormat) {
                    return Carbon::parse($item->created_at)->format($groupFormat);
                });

                $labels = [];
                $revenue = [];
                
                // If grouping is daily, let's fill in missing days so the chart doesn't have gaps
                if ($groupFormat === 'Y-m-d' && $daysDiff <= 62) {
                    $curr = $start->copy();
                    while ($curr->lte($end)) {
                        $dateStr = $curr->format('Y-m-d');
                        $labels[] = $curr->format('M d');
                        $revenue[] = isset($grouped[$dateStr]) ? $grouped[$dateStr]->sum('total') : 0;
                        $curr->addDay();
                    }
                } else {
                    foreach ($grouped as $key => $items) {
                        $labels[] = Carbon::parse($key . ($groupFormat === 'Y-m' ? '-01' : ''))->format($labelFormat);
                        $revenue[] = $items->sum('total');
                    }
                }

                return [
                    'type' => 'line',
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'SALES REVENUE',
                            'data' => $revenue,
                            'borderColor' => '#00e3fd',
                        ]
                    ]
                ];

            case 'profit':
                // Profit and loss components over time
                $invoices = Invoice::whereBetween('created_at', [$start, $end])
                    ->with('items.product')
                    ->orderBy('created_at')
                    ->get();

                $expenses = Expense::whereBetween('date_incurred', [$from, $to])
                    ->orderBy('date_incurred')
                    ->get();

                $salaries = Salary::whereBetween('payment_date', [$from, $to])
                    ->orderBy('payment_date')
                    ->get();

                // Group them by same period
                $labels = [];
                $revenue = [];
                $cost = [];
                $exps = [];
                $net = [];

                if ($groupFormat === 'Y-m-d' && $daysDiff <= 62) {
                    $curr = $start->copy();
                    while ($curr->lte($end)) {
                        $dateStr = $curr->format('Y-m-d');
                        $labels[] = $curr->format('M d');
                        
                        $dayInvoices = $invoices->filter(fn($i) => Carbon::parse($i->created_at)->format('Y-m-d') === $dateStr);
                        $dayExpenses = $expenses->filter(fn($e) => Carbon::parse($e->date_incurred)->format('Y-m-d') === $dateStr)->sum('amount');
                        $daySalaries = $salaries->filter(fn($s) => Carbon::parse($s->payment_date)->format('Y-m-d') === $dateStr)->sum('amount_paid');
                        
                        $dayRevenue = $dayInvoices->sum('total');
                        $dayCost = $dayInvoices->sum(function($inv) {
                            return $inv->items->sum(fn($item) => ($item->product ? $item->product->buying_price : 0) * $item->quantity);
                        });
                        $dayOverhead = $dayExpenses + $daySalaries;
                        $dayNet = $dayRevenue - $dayCost - $dayOverhead;

                        $revenue[] = $dayRevenue;
                        $cost[] = $dayCost;
                        $exps[] = $dayOverhead;
                        $net[] = $dayNet;

                        $curr->addDay();
                    }
                } else {
                    // Group by month/year
                    $groupedInvs = $invoices->groupBy(fn($i) => Carbon::parse($i->created_at)->format($groupFormat));
                    $groupedExps = $expenses->groupBy(fn($e) => Carbon::parse($e->date_incurred)->format($groupFormat));
                    $groupedSals = $salaries->groupBy(fn($s) => Carbon::parse($s->payment_date)->format($groupFormat));

                    $allKeys = collect($groupedInvs->keys())
                        ->merge($groupedExps->keys())
                        ->merge($groupedSals->keys())
                        ->unique()
                        ->sort();

                    foreach ($allKeys as $key) {
                        $labels[] = Carbon::parse($key . ($groupFormat === 'Y-m' ? '-01' : ''))->format($labelFormat);
                        
                        $periodInvs = $groupedInvs->get($key, collect());
                        $periodRevenue = $periodInvs->sum('total');
                        $periodCost = $periodInvs->sum(function($inv) {
                            return $inv->items->sum(fn($item) => ($item->product ? $item->product->buying_price : 0) * $item->quantity);
                        });
                        $periodExpenses = $groupedExps->get($key, collect())->sum('amount') + $groupedSals->get($key, collect())->sum('amount_paid');
                        $periodNet = $periodRevenue - $periodCost - $periodExpenses;

                        $revenue[] = $periodRevenue;
                        $cost[] = $periodCost;
                        $exps[] = $periodExpenses;
                        $net[] = $periodNet;
                    }
                }

                return [
                    'type' => 'line',
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'REVENUE',
                            'data' => $revenue,
                            'borderColor' => '#00e3fd',
                        ],
                        [
                            'label' => 'COGS (COST OF GOODS)',
                            'data' => $cost,
                            'borderColor' => '#fb923c',
                        ],
                        [
                            'label' => 'OVERHEAD EXPENSES',
                            'data' => $exps,
                            'borderColor' => '#f43f5e',
                        ],
                        [
                            'label' => 'NET PROFIT',
                            'data' => $net,
                            'borderColor' => '#10b981',
                        ]
                    ]
                ];

            case 'stock':
                // Top categories by stock value
                $products = Product::with('category')->get();
                $groupedCat = $products->groupBy(fn($p) => $p->category ? $p->category->name : 'Uncategorized');
                
                $labels = [];
                $costVal = [];
                $saleVal = [];
                
                foreach ($groupedCat as $catName => $items) {
                    $labels[] = $catName;
                    $costVal[] = $items->sum(fn($i) => $i->buying_price * $i->stock);
                    $saleVal[] = $items->sum(fn($i) => $i->price * $i->stock);
                }

                return [
                    'type' => 'bar',
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'STOCK COST VALUE',
                            'data' => $costVal,
                            'backgroundColor' => '#f43f5e',
                            'borderColor' => '#ef4444',
                        ],
                        [
                            'label' => 'STOCK RETAIL VALUE',
                            'data' => $saleVal,
                            'backgroundColor' => '#00e3fd',
                            'borderColor' => '#06b6d4',
                        ]
                    ]
                ];

            case 'product':
                // Top 10 products by qty sold
                $items = InvoiceItem::whereHas('invoice', function($q) use ($start, $end) {
                        $q->whereBetween('created_at', [$start, $end]);
                    })
                    ->select('product_id', DB::raw('SUM(quantity) as qty_sold'), DB::raw('SUM(total) as revenue'))
                    ->groupBy('product_id')
                    ->with('product')
                    ->orderBy('qty_sold', 'desc')
                    ->limit(10)
                    ->get();

                $labels = [];
                $qty = [];
                $rev = [];

                foreach ($items as $i) {
                    $labels[] = $i->product ? $i->product->name : 'Unknown Product';
                    $qty[] = $i->qty_sold;
                    $rev[] = $i->revenue;
                }

                return [
                    'type' => 'bar',
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'UNITS SOLD',
                            'data' => $qty,
                            'backgroundColor' => '#10b981',
                            'yAxisID' => 'y',
                        ],
                        [
                            'label' => 'REVENUE (Rs.)',
                            'data' => $rev,
                            'backgroundColor' => '#00e3fd',
                            'yAxisID' => 'y1',
                        ]
                    ]
                ];

            case 'customer_payment':
                // Payment methods distribution
                $methods = Invoice::whereBetween('created_at', [$start, $end])
                    ->select('payment_method', DB::raw('SUM(total) as total_volume'))
                    ->groupBy('payment_method')
                    ->get();

                $labels = [];
                $volumes = [];

                foreach ($methods as $m) {
                    $labels[] = strtoupper($m->payment_method);
                    $volumes[] = $m->total_volume;
                }

                return [
                    'type' => 'doughnut',
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'VOLUME (Rs.)',
                            'data' => $volumes,
                            'backgroundColor' => ['#00e3fd', '#a855f7', '#fb923c', '#ec4899', '#3b82f6'],
                        ]
                    ]
                ];

            case 'sales_ref':
                // Representative performance
                $refs = Invoice::whereBetween('created_at', [$start, $end])
                    ->whereNotNull('employee_id')
                    ->select('employee_id', DB::raw('SUM(total) as total_volume'))
                    ->groupBy('employee_id')
                    ->with('employee')
                    ->orderBy('total_volume', 'desc')
                    ->limit(10)
                    ->get();

                $labels = [];
                $volumes = [];

                foreach ($refs as $r) {
                    $labels[] = $r->employee ? $r->employee->name : 'Unknown';
                    $volumes[] = $r->total_volume;
                }

                return [
                    'type' => 'bar',
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'SALES VOLUME (Rs.)',
                            'data' => $volumes,
                            'backgroundColor' => '#a855f7',
                        ]
                    ]
                ];

            case 'customer_credit':
                // Top customers with outstanding credit
                $credits = Invoice::whereBetween('created_at', [$start, $end])
                    ->where(function($q) {
                        $q->where('is_paid', false)
                          ->orWhere('balance', '<', 0);
                    })
                    ->with('customer')
                    ->get()
                    ->groupBy(fn($i) => $i->customer ? $i->customer->name : 'Walk-in')
                    ->map(fn($group) => $group->sum(fn($i) => abs($i->balance)))
                    ->sortDesc()
                    ->take(10);

                return [
                    'type' => 'bar',
                    'labels' => $credits->keys()->toArray(),
                    'datasets' => [
                        [
                            'label' => 'OUTSTANDING BALANCE (Rs.)',
                            'data' => $credits->values()->toArray(),
                            'backgroundColor' => '#f43f5e',
                        ]
                    ]
                ];

            case 'supplier':
                // Procurement cost by Supplier
                $grns = Grn::whereBetween('date_received', [$from, $to])
                    ->with('supplier')
                    ->get()
                    ->groupBy(fn($g) => $g->supplier ? $g->supplier->name : 'Unknown')
                    ->map(fn($group) => $group->sum('total_amount'))
                    ->sortDesc()
                    ->take(10);

                return [
                    'type' => 'bar',
                    'labels' => $grns->keys()->toArray(),
                    'datasets' => [
                        [
                            'label' => 'PROCUREMENT SPEND (Rs.)',
                            'data' => $grns->values()->toArray(),
                            'backgroundColor' => '#fb923c',
                        ]
                    ]
                ];

            case 'sale_type':
                // Shop vs Online vs Corporate
                $types = Invoice::whereBetween('created_at', [$start, $end])
                    ->select('sale_type', DB::raw('SUM(total) as total_volume'))
                    ->groupBy('sale_type')
                    ->get();

                $labels = [];
                $volumes = [];

                foreach ($types as $t) {
                    $labels[] = strtoupper($t->sale_type ?: 'SHOP');
                    $volumes[] = $t->total_volume;
                }

                return [
                    'type' => 'doughnut',
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'SALES VOLUME (Rs.)',
                            'data' => $volumes,
                            'backgroundColor' => ['#00e3fd', '#10b981', '#fb923c', '#a855f7'],
                        ]
                    ]
                ];

            case 'return':
                // Returns over time
                $returns = ProductReturn::whereBetween('created_at', [$start, $end])
                    ->orderBy('created_at')
                    ->get();

                $grouped = $returns->groupBy(function($item) use ($groupFormat) {
                    return Carbon::parse($item->created_at)->format($groupFormat);
                });

                $labels = [];
                $refunds = [];

                if ($groupFormat === 'Y-m-d' && $daysDiff <= 62) {
                    $curr = $start->copy();
                    while ($curr->lte($end)) {
                        $dateStr = $curr->format('Y-m-d');
                        $labels[] = $curr->format('M d');
                        $refunds[] = isset($grouped[$dateStr]) ? $grouped[$dateStr]->sum('refund_amount') : 0;
                        $curr->addDay();
                    }
                } else {
                    foreach ($grouped as $key => $items) {
                        $labels[] = Carbon::parse($key . ($groupFormat === 'Y-m' ? '-01' : ''))->format($labelFormat);
                        $refunds[] = $items->sum('refund_amount');
                    }
                }

                return [
                    'type' => 'line',
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'REFUNDS PAID (Rs.)',
                            'data' => $refunds,
                            'borderColor' => '#f43f5e',
                        ]
                    ]
                ];

            case 'expenses':
                // Expenses breakdown by Category
                $exps = Expense::whereBetween('date_incurred', [$from, $to])
                    ->select('category', DB::raw('SUM(amount) as total_amount'))
                    ->groupBy('category')
                    ->get();

                $labels = [];
                $amounts = [];

                foreach ($exps as $e) {
                    $labels[] = strtoupper($e->category);
                    $amounts[] = $e->total_amount;
                }

                return [
                    'type' => 'doughnut',
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'EXPENSE AMOUNT (Rs.)',
                            'data' => $amounts,
                            'backgroundColor' => ['#f43f5e', '#fb923c', '#3b82f6', '#10b981', '#a855f7', '#64748b'],
                        ]
                    ]
                ];

            case 'salary':
                // Salary Disbursements by Employee
                $sals = Salary::whereBetween('payment_date', [$from, $to])
                    ->with('employee')
                    ->get()
                    ->groupBy(fn($s) => $s->employee ? $s->employee->name : 'Unknown')
                    ->map(fn($group) => $group->sum('amount_paid'))
                    ->sortDesc()
                    ->take(10);

                return [
                    'type' => 'bar',
                    'labels' => $sals->keys()->toArray(),
                    'datasets' => [
                        [
                            'label' => 'PAYROLL DISBURSED (Rs.)',
                            'data' => $sals->values()->toArray(),
                            'backgroundColor' => '#3b82f6',
                        ]
                    ]
                ];

            case 'attendance':
                // Average attendance rate by Employee
                $attends = Attendance::whereBetween('date', [$from, $to])
                    ->get()
                    ->groupBy(fn($a) => $a->employee ? $a->employee->name : 'Unknown');

                $labels = [];
                $rates = [];

                foreach ($attends as $empName => $logs) {
                    $total = $logs->count();
                    $present = $logs->filter(fn($l) => in_array($l->status, ['present', 'late']))->count();
                    $rate = $total > 0 ? ($present / $total) * 100 : 0;
                    
                    $labels[] = $empName;
                    $rates[] = round($rate, 1);
                }

                return [
                    'type' => 'bar',
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => 'ATTENDANCE RATE (%)',
                            'data' => $rates,
                            'backgroundColor' => '#10b981',
                        ]
                    ]
                ];
        }

        return null;
    }

    private function downloadCsv($type, $data, $from, $to)
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=report_{$type}_{$from}_to_{$to}.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($type, $data) {
            $file = fopen('php://output', 'w');

            switch ($type) {
                case 'sales':
                    fputcsv($file, ['Invoice Number', 'Date', 'Customer Name', 'Customer Contact', 'Sale Type', 'Payment Method', 'Paid', 'Subtotal', 'Tax', 'Discount', 'Total']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->invoice_number,
                            $row->created_at->format('Y-m-d H:i'),
                            $row->customer ? $row->customer->name : 'Walk-in',
                            $row->customer ? $row->customer->phone : 'N/A',
                            $row->sale_type,
                            $row->payment_method,
                            $row->is_paid ? 'Yes' : 'No',
                            $row->subtotal,
                            $row->tax,
                            $row->discount,
                            $row->total
                        ]);
                    }
                    break;

                case 'profit':
                    fputcsv($file, ['Product Name', 'Qty Sold', 'Buying Price', 'Selling Price', 'Revenue', 'Cost', 'Profit']);
                    foreach ($data['breakdown'] as $row) {
                        fputcsv($file, [
                            $row['product_name'],
                            $row['qty_sold'],
                            $row['buying_price'],
                            $row['selling_price'],
                            $row['revenue'],
                            $row['cost'],
                            $row['profit']
                        ]);
                    }
                    fputcsv($file, []);
                    fputcsv($file, ['Summary Metrics', 'Value']);
                    fputcsv($file, ['Total Sales Revenue', $data['total_revenue']]);
                    fputcsv($file, ['Total Buying Cost', $data['total_cost']]);
                    fputcsv($file, ['Total Expenses', $data['total_expenses']]);
                    fputcsv($file, ['Net Profit', $data['net_profit']]);
                    break;

                case 'stock':
                    fputcsv($file, ['Product Name', 'SKU', 'Category', 'Stock Qty', 'Buying Price', 'Selling Price', 'Cost Value', 'Sale Value']);
                    foreach ($data['products'] as $row) {
                        fputcsv($file, [
                            $row['name'],
                            $row['sku'],
                            $row['category'],
                            $row['stock'],
                            $row['buying_price'],
                            $row['sale_price'],
                            $row['cost_value'],
                            $row['sale_value']
                        ]);
                    }
                    fputcsv($file, []);
                    fputcsv($file, ['Total Cost Value', $data['total_cost_value']]);
                    fputcsv($file, ['Total Sale Value', $data['total_sale_value']]);
                    break;

                case 'product':
                    fputcsv($file, ['Product Name', 'SKU', 'Brand', 'Qty Sold', 'Free Qty Given', 'Total Revenue']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->product ? $row->product->name : 'Unknown',
                            $row->product ? $row->product->sku : 'N/A',
                            $row->product ? $row->product->brand : 'N/A',
                            $row->qty_sold,
                            $row->free_qty,
                            $row->revenue
                        ]);
                    }
                    break;

                case 'customer_payment':
                    fputcsv($file, ['Payment Method', 'Transaction Count', 'Total Sales Volume']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->payment_method,
                            $row->count,
                            $row->total_volume
                        ]);
                    }
                    break;

                case 'sales_ref':
                    fputcsv($file, ['Salesperson Name', 'Designation', 'Total Invoices Created', 'Total Sales Volume']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->employee ? $row->employee->name : 'Unknown',
                            $row->employee ? $row->employee->designation : 'N/A',
                            $row->count,
                            $row->total_volume
                        ]);
                    }
                    break;

                case 'customer_credit':
                    fputcsv($file, ['Invoice No', 'Date', 'Customer Name', 'Contact', 'Grand Total', 'Amount Paid', 'Outstanding Balance']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->invoice_number,
                            $row->created_at->format('Y-m-d'),
                            $row->customer ? $row->customer->name : 'Walk-in',
                            $row->customer ? $row->customer->phone : 'N/A',
                            $row->total,
                            $row->customer_paid,
                            $row->balance
                        ]);
                    }
                    break;

                case 'supplier':
                    fputcsv($file, ['GRN Number', 'Supplier', 'Received By', 'Date Received', 'Total Cost Amount']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->grn_number,
                            $row->supplier ? $row->supplier->name : 'N/A',
                            $row->receivedBy ? $row->receivedBy->name : 'N/A',
                            $row->date_received,
                            $row->total_amount
                        ]);
                    }
                    break;

                case 'sale_type':
                    fputcsv($file, ['Sale Type', 'Invoices Count', 'Total Sales Volume']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->sale_type,
                            $row->count,
                            $row->total_volume
                        ]);
                    }
                    break;

                case 'return':
                    fputcsv($file, ['Return No', 'Invoice Reference', 'Supplier Link', 'Type', 'Reason', 'Refund Amount', 'Date Returned']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->return_number,
                            $row->invoice ? $row->invoice->invoice_number : 'N/A',
                            $row->supplier ? $row->supplier->name : 'N/A',
                            $row->type,
                            $row->reason,
                            $row->refund_amount,
                            $row->created_at->format('Y-m-d')
                        ]);
                    }
                    break;

                case 'expenses':
                    fputcsv($file, ['Expense Category', 'Transactions Count', 'Total Amount']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->category,
                            $row->count,
                            $row->total_amount
                        ]);
                    }
                    break;

                case 'attendance':
                    fputcsv($file, ['Employee Name', 'Designation', 'Days Present', 'Days Late', 'Days Absent']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->employee ? $row->employee->name : 'Unknown',
                            $row->employee ? $row->employee->designation : 'N/A',
                            $row->present_days,
                            $row->late_days,
                            $row->absent_days
                        ]);
                    }
                    break;

                case 'salary':
                    fputcsv($file, ['Payslip No', 'Employee Name', 'Designation', 'Paid for Month', 'Payment Date', 'Payment Method', 'Paid Amount']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row->payslip_no,
                            $row->employee ? $row->employee->name : 'Unknown',
                            $row->employee ? $row->employee->designation : 'N/A',
                            $row->paid_for_month,
                            $row->payment_date,
                            $row->payment_method,
                            $row->amount_paid
                        ]);
                    }
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
