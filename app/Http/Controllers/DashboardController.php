<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();
        $startOfMonth = now()->startOfMonth();

        // --- Daily Stats ---
        $dailyRepairsReceived = \App\Models\RepairJob::whereDate('created_at', today())
            ->where('job_type', '!=', 'sale')
            ->count();
            
        $dailySalesCount = \App\Models\RepairJob::whereDate('created_at', today())
            ->where('job_type', 'sale')
            ->count();
        
        // Financials (All Jobs + Sales)
        $dailyBaseQuery = \App\Models\RepairJob::whereDate('completed_at', today())
            ->whereIn('repair_status', ['completed', 'delivered']);

        $dailyRepairsCompleted = (clone $dailyBaseQuery)->where('job_type', '!=', 'sale')->count();
            
        $dailyRevenue = $dailyBaseQuery->sum('final_price');
        $dailyCost = $dailyBaseQuery->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(parts_used_cost, 0) + COALESCE(labor_cost, 0)'));
        // Using explicit SUM returns scalar, so no accessor collision here, but consistent naming is good. 
        // Actually sum() just returns value.
        $dailyProfit = $dailyBaseQuery->sum(\Illuminate\Support\Facades\DB::raw('final_price - (COALESCE(parts_used_cost, 0) + COALESCE(labor_cost, 0))'));

        // --- Monthly Stats ---
        $monthlyRepairsReceived = \App\Models\RepairJob::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('job_type', '!=', 'sale')
            ->count();
            
        $monthlySalesCount = \App\Models\RepairJob::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('job_type', 'sale')
            ->count();
            
        $monthlyBaseQuery = \App\Models\RepairJob::whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->whereIn('repair_status', ['completed', 'delivered']);

        $monthlyRepairsCompleted = (clone $monthlyBaseQuery)->where('job_type', '!=', 'sale')->count();

        $monthlyRevenue = $monthlyBaseQuery->sum('final_price');
        $monthlyCost = $monthlyBaseQuery->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(parts_used_cost, 0) + COALESCE(labor_cost, 0)'));
        $monthlyProfit = $monthlyBaseQuery->sum(\Illuminate\Support\Facades\DB::raw('final_price - (COALESCE(parts_used_cost, 0) + COALESCE(labor_cost, 0))'));

        // --- Chart Data (Last 7 Days) ---
        $chartLabels = [];
        $chartRevenue = [];
        $chartProfit = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('D'); // Mon, Tue...
            
            // Use completed_at for stable revenue recognition
            // Alias changed to 'net_profit' to avoid collision with getProfitAttribute
            $dayStats = \App\Models\RepairJob::selectRaw('SUM(final_price) as revenue, SUM(final_price - COALESCE(parts_used_cost, 0) - COALESCE(labor_cost, 0)) as net_profit')
                ->whereDate('completed_at', $date)
                ->whereIn('repair_status', ['completed', 'delivered'])
                ->first();
                
            $chartRevenue[] = $dayStats->revenue ?? 0;
            $chartProfit[] = $dayStats->net_profit ?? 0;
        }

        return view('dashboard', compact(
            'dailyRepairsReceived', 'dailySalesCount', 'dailyRepairsCompleted', 'dailyCost', 'dailyProfit', 'dailyRevenue',
            'monthlyRepairsReceived', 'monthlySalesCount', 'monthlyRepairsCompleted', 'monthlyCost', 'monthlyProfit', 'monthlyRevenue',
            'chartLabels', 'chartRevenue', 'chartProfit'
        ));
    }
}
