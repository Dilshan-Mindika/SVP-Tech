<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait DateFilterable
{
    /**
     * Apply date range or preset filters to a query.
     */
    public function applyDateFilter(Builder $query, Request $request, string $dateColumn = 'created_at')
    {
        if ($request->has('date_filter') && !empty($request->date_filter)) {
            $preset = $request->date_filter;
            if ($preset === 'today') {
                $query->whereDate($dateColumn, Carbon::today());
            } elseif ($preset === 'weekly') {
                $query->whereBetween($dateColumn, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($preset === 'monthly') {
                $query->whereMonth($dateColumn, Carbon::now()->month)
                      ->whereYear($dateColumn, Carbon::now()->year);
            } elseif ($preset === 'annually') {
                $query->whereYear($dateColumn, Carbon::now()->year);
            } elseif ($preset === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween($dateColumn, [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }
        }
        return $query;
    }
}
