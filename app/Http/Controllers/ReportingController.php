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
}
