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
        
        $dailyCompletedJobs = \App\Models\RepairJob::whereDate('updated_at', today())
            ->where('repair_status', 'completed')
            ->get();
            
        $dailyJobsCompleted = $dailyCompletedJobs->count();
        $dailyRevenue = $dailyCompletedJobs->sum('final_price');
        $dailyCost = $dailyCompletedJobs->sum('parts_used_cost') + $dailyCompletedJobs->sum('labor_cost');
        
        // Profit: Final Price - Cost. If Final Price is 0 (not set), profit is likely negative or 0.
        // Assuming final_price is the billed amount.
        $dailyProfit = $dailyCompletedJobs->sum(function($job) {
            return $job->final_price - ($job->parts_used_cost + $job->labor_cost);
        });

        // --- Monthly Stats ---
        $monthlyJobsGot = \App\Models\RepairJob::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        $monthlyCompletedJobs = \App\Models\RepairJob::whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->where('repair_status', 'completed')
            ->get();
            
        $monthlyJobsCompleted = $monthlyCompletedJobs->count();
        $monthlyRevenue = $monthlyCompletedJobs->sum('final_price');
        $monthlyCost = $monthlyCompletedJobs->sum('parts_used_cost') + $monthlyCompletedJobs->sum('labor_cost');
        
        $monthlyProfit = $monthlyCompletedJobs->sum(function($job) {
            return $job->final_price - ($job->parts_used_cost + $job->labor_cost);
        });

        // --- Chart Data (Last 7 Days) ---
        $chartLabels = [];
        $chartRevenue = [];
        $chartProfit = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('D'); // Mon, Tue...
            
            $dayJobs = \App\Models\RepairJob::whereDate('updated_at', $date)
                ->where('repair_status', 'completed')
                ->get();
                
            $rev = $dayJobs->sum('final_price');
            $prof = $dayJobs->sum(function($job) {
                return $job->final_price - ($job->parts_used_cost + $job->labor_cost);
            });
            
            $chartRevenue[] = $rev;
            $chartProfit[] = $prof;
        }

        return view('dashboard', compact(
            'dailyJobsGot', 'dailyJobsCompleted', 'dailyCost', 'dailyProfit', 'dailyRevenue',
            'monthlyJobsGot', 'monthlyJobsCompleted', 'monthlyCost', 'monthlyProfit', 'monthlyRevenue',
            'chartLabels', 'chartRevenue', 'chartProfit'
        ));
    }
}
