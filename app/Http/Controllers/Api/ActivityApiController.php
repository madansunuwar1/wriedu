<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityApiController extends Controller
{
    /**
     * Get a paginated list of users with their online status and activity stats.
     * This is the main endpoint for the Activity Index page.
     */
    public function getUsersWithActivity(Request $request)
    {
        $query = User::query();

        // Handle Search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Handle Status Filter (Online/Offline)
        $fiveMinutesAgo = now()->subMinutes(5)->timestamp;
        if ($request->filled('status')) {
            $onlineUserIds = DB::table('sessions')
                ->where('last_activity', '>=', $fiveMinutesAgo)
                ->whereNotNull('user_id')
                ->pluck('user_id');

            if ($request->status === 'active') {
                $query->whereIn('id', $onlineUserIds);
            } elseif ($request->status === 'inactive') {
                $query->whereNotIn('id', $onlineUserIds);
            }
        }

        $users = $query->orderBy('name', 'asc')->paginate(9);

        // Enhance user data with both online status and activity stats
        $users->getCollection()->transform(function ($user) use ($fiveMinutesAgo) {
            // 1. Get Online Status & Last Activity from sessions table
            $lastSession = DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderBy('last_activity', 'desc')
                ->first();

            $user->is_online = $lastSession && $lastSession->last_activity >= $fiveMinutesAgo;
            $user->last_activity = $lastSession ? Carbon::createFromTimestamp($lastSession->last_activity) : null;
            $user->last_activity_human = $user->last_activity ? $user->last_activity->diffForHumans() : 'Never';

            // 2. Get Activity Log Stats from activity_log table
            $stats = Activity::query()
                ->where('causer_type', User::class)
                ->where('causer_id', $user->id)
                ->selectRaw("
        SUM(CASE WHEN description LIKE '%created%' THEN 1 ELSE 0 END) as created,
        SUM(CASE WHEN description LIKE '%comment%' THEN 1 ELSE 0 END) as commented,
        SUM(CASE WHEN description LIKE '%updated%' THEN 1 ELSE 0 END) as updated, -- <-- ADD THIS LINE
        SUM(CASE WHEN description LIKE '%status%' THEN 1 ELSE 0 END) as status_updated -- Make this more general
    ")
                ->first();

            $user->activity_stats = [
                'created' => (int)($stats->created ?? 0),
                'commented' => (int)($stats->commented ?? 0),
                'updated' => (int)($stats->updated ?? 0), // <-- ADD THIS LINE
                'status_updated' => (int)($stats->status_updated ?? 0),
            ];

            return $user;
        });

        return response()->json($users);
    }

    /**
     * Check which users are currently online for real-time updates on the frontend.
     */
    public function checkOnlineUsers()
    {
        try {
            $fiveMinutesAgo = now()->subMinutes(5)->timestamp;

            $onlineUserIds = DB::table('sessions')
                ->where('last_activity', '>=', $fiveMinutesAgo)
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->toArray();

            $allUserSessions = DB::table('sessions')
                ->whereNotNull('user_id')
                ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

            $userActivities = [];
            foreach ($allUserSessions as $userId => $session) {
                $userActivities[$userId] = [
                    'is_online' => in_array($userId, $onlineUserIds),
                    'last_activity_human' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            }

            return response()->json([
                'success' => true,
                'user_statuses' => $userActivities,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking online users: ' . $e->getMessage()
            ], 500);
        }
    }

    // --- Methods for the Activity RECORD Page ---
    // These will be used by your ActivityRecord.vue component

    /**
     * Get a paginated list of activity logs for a specific user or all users.
     */
    public function getLogs(Request $request)
    {
        $query = Activity::with('causer')->latest();

        if ($request->filled('user_id')) {
            // --- FIX: ADD THE CAUSER_TYPE FILTER ---
            $query->where('causer_type', User::class)
                ->where('causer_id', $request->user_id);
        }
        if ($request->filled('description')) {
            $query->where('description', $request->description);
        }
        $this->applyDateFilters($query, $request);

        $logs = $query->paginate(15)->withQueryString();
        return response()->json($logs);
    }
    /**
     * Get distinct activity descriptions for filter dropdowns.
     */
    public function getDescriptions()
    {
        $descriptions = Activity::select('description')
            ->distinct()
            ->whereNotNull('description')
            ->orderBy('description')
            ->pluck('description')
            ->filter()
            ->values();
        return response()->json($descriptions);
    }

    /**
     * Calculate and return activity statistics for a specific user or all users.
     */
    public function getStats(Request $request)
    {
        $query = Activity::query();

        if ($request->filled('user_id')) {
            $query->where('causer_type', User::class)
                ->where('causer_id', $request->user_id);
        }

        $this->applyDateFilters($query, $request);

        // Use simplified and consistent logic that matches the index page.
        $activityStats = $query->selectRaw("
    SUM(CASE WHEN description LIKE '%created%' THEN 1 ELSE 0 END) as created,
    SUM(CASE WHEN description LIKE '%comment%' THEN 1 ELSE 0 END) as commented,
    SUM(CASE WHEN description LIKE '%updated%' THEN 1 ELSE 0 END) as updated, -- <-- ADD THIS LINE
    SUM(CASE WHEN description LIKE '%status%' THEN 1 ELSE 0 END) as status_updated, -- Make this more general
    COUNT(*) as total
    ")->first();

        return response()->json([
            'created' => (int)($activityStats->created ?? 0),
            'commented' => (int)($activityStats->commented ?? 0),
            'updated' => (int)($activityStats->updated ?? 0), // <-- ADD THIS LINE
            'status_updated' => (int)($activityStats->status_updated ?? 0),
            'total' => (int)($activityStats->total ?? 0),
        ]);
    }

    /**
     * Helper method to apply date filters to a query.
     */
    private function applyDateFilters($query, Request $request)
    {
        if ($request->filled('period')) {
            $period = $request->period;
            $now = Carbon::now();
            switch ($period) {
                case 'day':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->copy()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('created_at', [$now->startOfMonth(), $now->copy()->endOfMonth()]);
                    break;
                case 'year':
                    $query->whereBetween('created_at', [$now->startOfYear(), $now->copy()->endOfYear()]);
                    break;
            }
        } elseif ($request->filled('date_from') || $request->filled('date_to')) {
            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
            }
            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
            }
        }
    }
}
