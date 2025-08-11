<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            Log::info('ActivityLogController: index method called');

            $query = Activity::with('causer')->latest();

             // Apply user filter from the route parameter
        if ($request->route('user')) {
            $query->where('causer_id', $request->route('user'));
        }

            // Apply user filter if specified
            if ($request->filled('user_filter') && $request->user_filter !== 'All Users') {
                $query->where('causer_id', $request->user_filter);
            }

            // Apply action filter if specified
            if ($request->filled('action_filter') && $request->action_filter !== 'All Actions') {
                $query->where('description', $request->action_filter);
            }

            // Apply date filters
            $this->applyDateFilters($query, $request);

            // Force pagination with query string preservation
            $logs = $query->paginate(15)->withQueryString();

            $users = User::select('id', 'name', 'email')->orderBy('name')->get();

            $descriptions = Activity::select('description')
                ->distinct()
                ->whereNotNull('description')
                ->orderBy('description')
                ->pluck('description')
                ->filter() // Remove empty values
                ->toArray();

            $activityStats = $this->calculateActivityStats($request->filled('user_filter') ? $request->user_filter : null, $request);

             $selectedUser = null;
        if ($request->filled('user_filter') && $request->user_filter !== 'All Users') {
            $selectedUser = User::find($request->user_filter);
        }

            Log::info('ActivityLogController: index completed successfully', [
                'logs_count' => $logs->total(),
                'users_count' => $users->count(),
                'logs_type' => get_class($logs)
            ]);

            return view('backend.activity.index', compact(
                'logs',
                'users',
                'descriptions',
                'selectedUser',
                'activityStats'
            ));

        } catch (\Exception $e) {
            Log::error('ActivityLogController: index failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to load activity logs. Please try again.');
        }
    }

   public function record(Request $request)
{
    try {
        Log::info('ActivityLogController: record method called');

        $query = Activity::with('causer')->latest();

        // Apply user filter from the route parameter
        if ($request->route('user')) {
            $query->where('causer_id', $request->route('user'));
        }

        // Apply action filter if specified
        if ($request->filled('action_filter') && $request->action_filter !== 'All Actions') {
            $query->where('description', $request->action_filter);
        }

        // Apply date filters
        $this->applyDateFilters($query, $request);

        // Force pagination with query string preservation
        $logs = $query->paginate(15)->withQueryString();

        // Get the specific user if user filter is applied
        $selectedUser = null;
        if ($request->route('user')) {
            $selectedUser = User::find($request->route('user'));
        }

        $activityStats = $this->calculateActivityStats($request->route('user'), $request);

        Log::info('ActivityLogController: record completed successfully', [
            'logs_count' => $logs->total(),
            'logs_type' => get_class($logs)
        ]);

        return view('backend.activity.record', compact(
            'logs',
            'selectedUser',
            'activityStats'
        ));

    } catch (\Exception $e) {
        Log::error('ActivityLogController: record failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'Failed to load activity logs. Please try again.');
    }
}

    private function applyDateFilters($query, Request $request)
    {
        // Handle time period filtering
        if ($request->filled('period')) {
            $period = $request->period;
            $now = Carbon::now();

            switch ($period) {
                case 'day':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [
                        $now->startOfWeek()->toDateTimeString(),
                        $now->copy()->endOfWeek()->toDateTimeString()
                    ]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [
                        $now->startOfMonth()->toDateTimeString(),
                        $now->copy()->endOfMonth()->toDateTimeString()
                    ]);
                    break;
                case 'year':
                    $query->whereBetween('created_at', [
                        $now->startOfYear()->toDateTimeString(),
                        $now->copy()->endOfYear()->toDateTimeString()
                    ]);
                    break;
            }
        }
        // Handle custom date range
        elseif ($request->filled('date_from') || $request->filled('date_to')) {
            if ($request->filled('date_from')) {
                $dateFrom = Carbon::parse($request->date_from)->startOfDay();
                $query->where('created_at', '>=', $dateFrom);
            }
            if ($request->filled('date_to')) {
                $dateTo = Carbon::parse($request->date_to)->endOfDay();
                $query->where('created_at', '<=', $dateTo);
            }
        }
    }

    private function calculateActivityStats($user_id, Request $request)
    {
        try {
            $query = Activity::query();
            if ($user_id) {
            $query->where('causer_id', $user_id);
        }

            // Apply the same filters as the main query
            $this->applyDateFilters($query, $request);

            $activityStats = $query->select([
                DB::raw("SUM(CASE WHEN description LIKE '%created%' THEN 1 ELSE 0 END) as created"),
                DB::raw("SUM(CASE WHEN description LIKE '%updated%' AND description NOT LIKE '%document status%' THEN 1 ELSE 0 END) as updated"),
                DB::raw("SUM(CASE WHEN description LIKE '%commented%' THEN 1 ELSE 0 END) as commented"),
                DB::raw("SUM(CASE WHEN description LIKE '%updated the document status%' THEN 1 ELSE 0 END) as status_updated"),
                DB::raw("COUNT(*) as total")
            ])->first();

            // Ensure we have a valid result
            if (!$activityStats) {
                return $this->getDefaultStats();
            }

            $activityStats = $activityStats->toArray();

            // Calculate changes based on the previous period
            $previousStats = $this->getPreviousPeriodStats($request);

            $activityStats['created_change'] = $this->calculateChange($activityStats['created'], $previousStats['created'] ?? 0);
            $activityStats['updated_change'] = $this->calculateChange($activityStats['updated'], $previousStats['updated'] ?? 0);
            $activityStats['commented_change'] = $this->calculateChange($activityStats['commented'], $previousStats['commented'] ?? 0);
            $activityStats['status_updated_change'] = $this->calculateChange($activityStats['status_updated'], $previousStats['status_updated'] ?? 0);
            $activityStats['total_change'] = $this->calculateChange($activityStats['total'], $previousStats['total'] ?? 0);

            return $activityStats;

        } catch (\Exception $e) {
            Log::error('ActivityLogController: calculateActivityStats failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getDefaultStats();
        }
    }

    private function getPreviousPeriodStats(Request $request)
    {
        try {
            $previousPeriodQuery = Activity::query();
            $now = Carbon::now();

            if ($request->filled('period')) {
                $period = $request->period;
                
                switch ($period) {
                    case 'day':
                        $previousDate = $now->copy()->subDay();
                        $previousPeriodQuery->whereDate('created_at', $previousDate->toDateString());
                        break;
                    case 'week':
                        $previousWeekStart = $now->copy()->subWeek()->startOfWeek();
                        $previousWeekEnd = $now->copy()->subWeek()->endOfWeek();
                        $previousPeriodQuery->whereBetween('created_at', [
                            $previousWeekStart->toDateTimeString(),
                            $previousWeekEnd->toDateTimeString()
                        ]);
                        break;
                    case 'month':
                        $previousMonthStart = $now->copy()->subMonth()->startOfMonth();
                        $previousMonthEnd = $now->copy()->subMonth()->endOfMonth();
                        $previousPeriodQuery->whereBetween('created_at', [
                            $previousMonthStart->toDateTimeString(),
                            $previousMonthEnd->toDateTimeString()
                        ]);
                        break;
                    case 'year':
                        $previousYearStart = $now->copy()->subYear()->startOfYear();
                        $previousYearEnd = $now->copy()->subYear()->endOfYear();
                        $previousPeriodQuery->whereBetween('created_at', [
                            $previousYearStart->toDateTimeString(),
                            $previousYearEnd->toDateTimeString()
                        ]);
                        break;
                }
            }
            elseif ($request->filled('date_from') && $request->filled('date_to')) {
                $dateFrom = Carbon::parse($request->date_from);
                $dateTo = Carbon::parse($request->date_to);
                $days = $dateFrom->diffInDays($dateTo);
                
                $previousStart = $dateFrom->copy()->subDays($days + 1);
                $previousEnd = $dateTo->copy()->subDays($days + 1);
                
                $previousPeriodQuery->whereBetween('created_at', [
                    $previousStart->startOfDay()->toDateTimeString(),
                    $previousEnd->endOfDay()->toDateTimeString()
                ]);
            }

            $previousStats = $previousPeriodQuery->select([
                DB::raw("SUM(CASE WHEN description LIKE '%created%' THEN 1 ELSE 0 END) as created"),
                DB::raw("SUM(CASE WHEN description LIKE '%updated%' AND description NOT LIKE '%document status%' THEN 1 ELSE 0 END) as updated"),
                DB::raw("SUM(CASE WHEN description LIKE '%commented%' THEN 1 ELSE 0 END) as commented"),
                DB::raw("SUM(CASE WHEN description LIKE '%updated the document status%' THEN 1 ELSE 0 END) as status_updated"),
                DB::raw("COUNT(*) as total")
            ])->first();

            return $previousStats ? $previousStats->toArray() : [
                'created' => 0,
                'updated' => 0,
                'commented' => 0,
                'status_updated' => 0,
                'total' => 0
            ];

        } catch (\Exception $e) {
            Log::error('ActivityLogController: getPreviousPeriodStats failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'created' => 0,
                'updated' => 0,
                'commented' => 0,
                'status_updated' => 0,
                'total' => 0
            ];
        }
    }

    private function calculateChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? '+∞' : '0%';
        }

        $change = (($current - $previous) / $previous) * 100;
        return sprintf('%s%.2f%%', $change >= 0 ? '+' : '', $change);
    }

    private function getDefaultStats()
    {
        return [
            'created' => 0,
            'updated' => 0,
            'commented' => 0,
            'status_updated' => 0,
            'total' => 0,
            'created_change' => '+0%',
            'updated_change' => '+0%',
            'commented_change' => '+0%',
            'status_updated_change' => '+0%',
            'total_change' => '+0%'
        ];
    }

    /**
     * Debug method to check what type of object we're getting
     */
    private function debugLogsObject($logs, $method = 'unknown')
    {
        Log::info("Debug logs object in {$method}", [
            'type' => get_class($logs),
            'has_total_method' => method_exists($logs, 'total'),
            'has_count_method' => method_exists($logs, 'count'),
            'has_links_method' => method_exists($logs, 'links'),
            'is_paginated' => $logs instanceof \Illuminate\Pagination\LengthAwarePaginator,
            'is_collection' => $logs instanceof \Illuminate\Database\Eloquent\Collection,
        ]);
    }
}