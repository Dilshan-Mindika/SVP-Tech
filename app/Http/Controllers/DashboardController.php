<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Salary;
use App\Models\Product;
use App\Models\Repair;
use App\Models\WarrantyClaim;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Financial KPIs
        $dailyRevenue = Invoice::whereDate('created_at', $today)->sum('total');
        $monthlyRevenue = Invoice::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total');
        
        $monthlyDirectExpenses = Expense::whereBetween('date_incurred', [$startOfMonth, $endOfMonth])->sum('amount');
        $monthlySalaries = Salary::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->sum('amount_paid');
        $totalMonthlyExpenses = $monthlyDirectExpenses + $monthlySalaries;

        // Fetch invoice items from this month to calculate COGS (Cost of Goods Sold)
        $monthlyInvoices = Invoice::whereBetween('created_at', [$startOfMonth, $endOfMonth])->with('items.product')->get();
        $monthlyCogs = 0;
        foreach ($monthlyInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                if ($item->product) {
                    $monthlyCogs += ($item->product->buying_price * $item->quantity);
                }
            }
        }
        
        $netProfit = $monthlyRevenue - $totalMonthlyExpenses - $monthlyCogs;

        // Operational counts
        $activeRepairsCount = Repair::whereIn('status', ['received', 'diagnosing', 'repairing', 'ready'])->count();
        $pendingWarrantiesCount = WarrantyClaim::where('status', 'pending')->count();
        $upcomingAppointmentsCount = Appointment::where('status', 'scheduled')
            ->where('appointment_time', '>=', Carbon::now())
            ->count();
        
        // Low Stock alert
        $lowStockProducts = Product::where('stock', '<', 5)->get();
        $lowStockCount = $lowStockProducts->count();

        // System Alerts Collection (Limit categories to prevent extreme page length)
        $alerts = [];
        
        // 1. Low Stock alerts (Limit to 5 individual alerts, then summarize)
        $lowStockToShow = $lowStockProducts->take(5);
        foreach ($lowStockToShow as $prod) {
            $alerts[] = [
                'type' => 'danger',
                'module' => 'Inventory',
                'message' => "Low stock: {$prod->name} ({$prod->stock} remaining, SKU: {$prod->sku})",
                'time' => 'System Check'
            ];
        }
        if ($lowStockCount > 5) {
            $remaining = $lowStockCount - 5;
            $alerts[] = [
                'type' => 'danger',
                'module' => 'Inventory',
                'message' => "Plus {$remaining} more low stock products in inventory.",
                'time' => 'System Check'
            ];
        }

        // 2. Unassigned Repair alerts (Limit to 5)
        $pendingRepairs = Repair::where('status', 'received')->take(5)->get();
        foreach ($pendingRepairs as $rep) {
            $alerts[] = [
                'type' => 'warning',
                'module' => 'Repairs',
                'message' => "Unassigned Repair Job: #{$rep->repair_job_no} for {$rep->device_model} ({$rep->customer_name})",
                'time' => $rep->created_at ? $rep->created_at->diffForHumans() : 'N/A'
            ];
        }

        // 3. Pending Warranty Claim alerts (Limit to 5)
        $pendingClaims = WarrantyClaim::where('status', 'pending')->with('customer')->take(5)->get();
        foreach ($pendingClaims as $claim) {
            $alerts[] = [
                'type' => 'info',
                'module' => 'Warranty',
                'message' => "Pending claim #{$claim->claim_number} from customer {$claim->customer->name}",
                'time' => $claim->created_at ? $claim->created_at->diffForHumans() : 'N/A'
            ];
        }

        // 4. Scheduled Appointments (Limit to 5)
        $todayAppointments = Appointment::whereDate('appointment_time', $today)
            ->where('status', 'scheduled')
            ->orderBy('appointment_time', 'asc')
            ->get();
        $todayApptsCount = $todayAppointments->count();
        foreach ($todayAppointments->take(5) as $apt) {
            $timeStr = Carbon::parse($apt->appointment_time)->format('h:i A');
            $alerts[] = [
                'type' => 'success',
                'module' => 'Appointments',
                'message' => "Scheduled consultation today at {$timeStr} with {$apt->customer_name} ({$apt->reason})",
                'time' => 'Today'
            ];
        }
        if ($todayApptsCount > 5) {
            $remaining = $todayApptsCount - 5;
            $alerts[] = [
                'type' => 'success',
                'module' => 'Appointments',
                'message' => "Plus {$remaining} more scheduled appointments today.",
                'time' => 'Today'
            ];
        }

        // Slice alerts to maximum 15 logs total to keep dashboard visually clean
        $alerts = array_slice($alerts, 0, 15);

        // Recent Activity lists (already limited to 5)
        $recentInvoices = Invoice::latest()->take(5)->with('customer')->get();
        $recentRepairs = Repair::latest()->take(5)->get();

        // ----------------------------------------------------
        // Chart Data Calculation (Optimized & Database-agnostic)
        // ----------------------------------------------------
        $fiveYearsAgo = Carbon::now()->subYears(4)->startOfYear();
        $allInvoices = Invoice::where('created_at', '>=', $fiveYearsAgo)->get();
        $allExpenses = Expense::where('date_incurred', '>=', $fiveYearsAgo)->get();
        $allSalaries = Salary::where('payment_date', '>=', $fiveYearsAgo)->get();

        $sumInvoices = function ($start, $end) use ($allInvoices) {
            return $allInvoices->filter(function ($inv) use ($start, $end) {
                return $inv->created_at >= $start && $inv->created_at <= $end;
            })->sum('total');
        };

        $sumExpenses = function ($start, $end) use ($allExpenses, $allSalaries) {
            $direct = $allExpenses->filter(function ($exp) use ($start, $end) {
                return $exp->date_incurred >= $start && $exp->date_incurred <= $end;
            })->sum('amount');

            $salary = $allSalaries->filter(function ($sal) use ($start, $end) {
                return $sal->payment_date >= $start && $sal->payment_date <= $end;
            })->sum('amount_paid');

            return $direct + $salary;
        };

        // 1. Daily (Last 7 Days)
        $dailyLabels = [];
        $dailyRevenueData = [];
        $dailyExpenseData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $start = $day->copy()->startOfDay();
            $end = $day->copy()->endOfDay();
            $dailyLabels[] = $day->format('d M');
            $dailyRevenueData[] = round($sumInvoices($start, $end), 2);
            $dailyExpenseData[] = round($sumExpenses($start, $end), 2);
        }

        // 2. Weekly (Last 4 Weeks)
        $weeklyLabels = [];
        $weeklyRevenueData = [];
        $weeklyExpenseData = [];
        for ($i = 3; $i >= 0; $i--) {
            $week = Carbon::today()->subWeeks($i);
            $start = $week->copy()->startOfWeek();
            $end = $week->copy()->endOfWeek();
            $weeklyLabels[] = 'Week ' . $start->format('d M');
            $weeklyRevenueData[] = round($sumInvoices($start, $end), 2);
            $weeklyExpenseData[] = round($sumExpenses($start, $end), 2);
        }

        // 3. Monthly (Last 6 Months)
        $monthlyLabels = [];
        $monthlyRevenueData = [];
        $monthlyExpenseData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            $monthlyLabels[] = $month->format('M Y');
            $monthlyRevenueData[] = round($sumInvoices($start, $end), 2);
            $monthlyExpenseData[] = round($sumExpenses($start, $end), 2);
        }

        // 4. Annually (Last 5 Years)
        $annualLabels = [];
        $annualRevenueData = [];
        $annualExpenseData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::today()->subYears($i);
            $start = $year->copy()->startOfYear();
            $end = $year->copy()->endOfYear();
            $annualLabels[] = $year->format('Y');
            $annualRevenueData[] = round($sumInvoices($start, $end), 2);
            $annualExpenseData[] = round($sumExpenses($start, $end), 2);
        }

        $chartData = [
            'daily' => [
                'labels' => $dailyLabels,
                'revenue' => $dailyRevenueData,
                'expenses' => $dailyExpenseData
            ],
            'weekly' => [
                'labels' => $weeklyLabels,
                'revenue' => $weeklyRevenueData,
                'expenses' => $weeklyExpenseData
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'revenue' => $monthlyRevenueData,
                'expenses' => $monthlyExpenseData
            ],
            'annually' => [
                'labels' => $annualLabels,
                'revenue' => $annualRevenueData,
                'expenses' => $annualExpenseData
            ]
        ];

        return view('dashboard', compact(
            'dailyRevenue',
            'monthlyRevenue',
            'totalMonthlyExpenses',
            'netProfit',
            'activeRepairsCount',
            'pendingWarrantiesCount',
            'upcomingAppointmentsCount',
            'lowStockCount',
            'alerts',
            'recentInvoices',
            'recentRepairs',
            'chartData'
        ));
    }
}
