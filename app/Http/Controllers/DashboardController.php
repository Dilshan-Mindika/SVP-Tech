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
        $dailyJobsGot = \App\Models\RepairJob::whereDate('created_at', today())->count();
        
        $dailyBaseQuery = \App\Models\RepairJob::whereDate('updated_at', today())
            ->whereIn('repair_status', ['completed', 'delivered']);
            
        $dailyJobsCompleted = $dailyBaseQuery->count();
        $dailyRevenue = $dailyBaseQuery->sum('final_price');
        $dailyCost = $dailyBaseQuery->sum(\Illuminate\Support\Facades\DB::raw('parts_used_cost + labor_cost'));
        $dailyProfit = $dailyBaseQuery->sum(\Illuminate\Support\Facades\DB::raw('final_price - (parts_used_cost + labor_cost)'));

        // --- Monthly Stats ---
        $monthlyJobsGot = \App\Models\RepairJob::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        $monthlyBaseQuery = \App\Models\RepairJob::whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->whereIn('repair_status', ['completed', 'delivered']);
            
        $monthlyJobsCompleted = $monthlyBaseQuery->count();
        $monthlyRevenue = $monthlyBaseQuery->sum('final_price');
        $monthlyCost = $monthlyBaseQuery->sum(\Illuminate\Support\Facades\DB::raw('parts_used_cost + labor_cost'));
        $monthlyProfit = $monthlyBaseQuery->sum(\Illuminate\Support\Facades\DB::raw('final_price - (parts_used_cost + labor_cost)'));

        // --- Chart Data (Last 7 Days) ---
        $chartLabels = [];
        $chartRevenue = [];
        $chartProfit = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('D'); // Mon, Tue...
            
            $dayStats = \App\Models\RepairJob::selectRaw('SUM(final_price) as revenue, SUM(final_price - parts_used_cost - labor_cost) as profit')
                ->whereDate('updated_at', $date)
                ->whereIn('repair_status', ['completed', 'delivered'])
                ->first();
                
            $chartRevenue[] = $dayStats->revenue ?? 0;
            $chartProfit[] = $dayStats->profit ?? 0;
        }

        return view('dashboard', compact(
            'dailyJobsGot', 'dailyJobsCompleted', 'dailyCost', 'dailyProfit', 'dailyRevenue',
            'monthlyJobsGot', 'monthlyJobsCompleted', 'monthlyCost', 'monthlyProfit', 'monthlyRevenue',
            'chartLabels', 'chartRevenue', 'chartProfit'
        ));
    }
}
