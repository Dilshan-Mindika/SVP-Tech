<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RepairJob;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportingController extends Controller
{
    public function index()
    {
        // Date Ranges
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Financials (Revenue)
        $dailyRevenue = Invoice::whereDate('created_at', $today)->sum('total_amount');
        $weeklyRevenue = Invoice::whereDate('created_at', '>=', $startOfWeek)->sum('total_amount');
        $monthlyRevenue = Invoice::whereDate('created_at', '>=', $startOfMonth)->sum('total_amount');

        // Profits
        $dailyProfit = Invoice::whereDate('created_at', $today)->sum('profit_margin');
        $monthlyProfit = Invoice::whereDate('created_at', '>=', $startOfMonth)->sum('profit_margin');

        // Job Stats
        $jobsCompletedToday = RepairJob::where('repair_status', 'completed')
            ->whereDate('updated_at', $today)
            ->count();
            
        $activeJobs = RepairJob::whereIn('repair_status', ['pending', 'in_progress'])->count();

        return view('reports.index', compact(
            'dailyRevenue', 'weeklyRevenue', 'monthlyRevenue',
            'dailyProfit', 'monthlyProfit',
            'jobsCompletedToday', 'activeJobs'
        ));
    }
    public function outstandingInvoices(Request $request)
    {
        $customerType = $request->input('customer_type', 'all');
        $toDate = $request->input('to_date', now()->format('Y-m-d'));

        $customersQuery = \App\Models\Customer::query()
            ->with(['repairJobs.invoices' => function($q) {
                // We only care about invoices that are NOT fully paid
                $q->where('status', '!=', 'paid')
                  ->with('payments');
            }])
            ->whereHas('repairJobs.invoices', function($q) {
                $q->where('status', '!=', 'paid');
            });

        if ($customerType !== 'all') {
            $customersQuery->where('type', $customerType);
        }

        $customers = $customersQuery->orderBy('name')->get();

        // Calculate Stats
        $totalOutstanding = 0;
        $totalOverdueInvoices = 0;
        $customersWithDebtCount = 0;

        foreach ($customers as $customer) {
            $customerTotal = $customer->repairJobs->flatMap->invoices->where('status', '!=', 'paid')->sum('balance_due');
            if ($customerTotal > 0) {
                $totalOutstanding += $customerTotal;
                $customersWithDebtCount++;
                $totalOverdueInvoices += $customer->repairJobs->flatMap->invoices->where('status', '!=', 'paid')->count();
            }
        }

        return view('reports.outstanding_invoices', compact('customers', 'customerType', 'toDate', 'totalOutstanding', 'customersWithDebtCount', 'totalOverdueInvoices'));
    }
}
