<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\CASFeedback;
use App\Models\CommissionTransaction;
use App\Models\Enquiry;
use App\Models\Lead;
use App\Models\RawLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * The single entry point for the entire dashboard.
     * Gathers all required metrics in one efficient call.
     */
    public function getDashboardStats(Request $request)
    {
        $cacheKey = 'dashboard_stats_single_page_' . auth()->id() . '_' . md5($request->fullUrl());

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            
            // --- GATHER ALL QUERIES ---
            $enquiryQuery = Enquiry::query();
            $this->applyDateFiltersToQuery($enquiryQuery, $request);
            
            $rawLeadQuery = RawLead::query();
            $this->applyDateFiltersToQuery($rawLeadQuery, $request);

            $leadQuery = $this->getRoleBasedLeadQuery($request);
            $appQuery = $this->getRoleBasedApplicationQuery($request);
            
            $activityQuery = Activity::query();
            $this->applyDateFiltersToQuery($activityQuery, $request);

            $casQuery = CASFeedback::query();
            $this->applyDateFiltersToQuery($casQuery, $request);

            // --- CALCULATE ALL METRICS ---
            
            // Financials (current balance, not date-filtered)
            $receivable = CommissionTransaction::where('type', 'receivable')->sum(DB::raw('amount - paid'));
            $payable = CommissionTransaction::where('type', 'payable')->sum(DB::raw('amount - paid'));

            // Sales & Pipeline
            $totalLeads = $leadQuery->clone()->count();
            $forwardedLeads = $leadQuery->clone()->where('is_forwarded', true)->count();
            
            // Operations & Applications
            $totalApps = $appQuery->clone()->count();
            $successfulApps = $appQuery->clone()->where('status', 'Visa Granted')->count();

            // --- ASSEMBLE THE FINAL JSON RESPONSE ---
            $data = [
                'kpis_executive' => [
                    'newEnquiries' => $enquiryQuery->count(),
                    'applications' => $totalApps,
                    'visaGranted' => $successfulApps,
                    'commissionReceivable' => round($receivable, 2),
                    'commissionPayable' => round($payable, 2),
                    'netPosition' => round($receivable - $payable, 2),
                    'usersOnline' => DB::table('sessions')->where('last_activity', '>=', now()->subMinutes(5)->timestamp)->whereNotNull('user_id')->count(),
                ],
                'kpis_sales' => [
                    'rawLeads' => $rawLeadQuery->count(),
                    'activeLeads' => $totalLeads,
                    'conversionRate' => $totalLeads > 0 ? round(($forwardedLeads / $totalLeads) * 100, 2) : 0,
                    'avgConversionTime' => $this->calculateAvgConversionTime($leadQuery->clone()),
                ],
                'charts_main' => [
                    'activityOverTime' => $activityQuery->groupBy('date')->orderBy('date', 'ASC')->get([DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count')])->pluck('count', 'date'),
                ],
                'charts_sales' => [
                    'leadStatusDistribution' => $leadQuery->clone()->select('status', DB::raw('count(*) as count'))->whereNotNull('status')->groupBy('status')->pluck('count', 'status'),
                    'leaderboard' => $this->getLeaderboard($request),
                ],
                'charts_operations' => [
                    'applicationStatusDistribution' => $this->getApplicationStatusDistribution($appQuery->clone()),
                    'casStatusDistribution' => $casQuery->clone()->select('status', DB::raw('count(*) as count'))->whereNotNull('status')->groupBy('status')->pluck('count', 'status'),
                    'topUniversities' => $appQuery->clone()->select('university', DB::raw('count(*) as count'))->whereNotNull('university')->groupBy('university')->orderBy('count', 'desc')->limit(5)->pluck('count', 'university'),
                    'topCountries' => $appQuery->clone()->select('country', DB::raw('count(*) as count'))->whereNotNull('country')->groupBy('country')->orderBy('count', 'desc')->limit(5)->pluck('count', 'country'),
                ]
            ];
            
            return response()->json($data);
        });
    }

    // =================================================================
    // REUSABLE QUERY & CALCULATION HELPERS
    // =================================================================

    private function getRoleBasedLeadQuery(Request $request)
    {
        $query = Lead::query();
        $user = auth()->user();
        $this->applyDateFiltersToQuery($query, $request);
        
        if ($request->filled('user_id') && $user->hasRole(['Administrator', 'Leads Manager', 'Manager'])) {
            $query->where('created_by', $request->user_id);
        }

        if ($user->hasRole(['Administrator', 'Leads Manager', 'Manager'])) {} 
        elseif ($user->hasRole('Front Desk (Receptionist)')) {
            $query->whereIn('status', ['Phone Not Received 2', 'Phone Not Received 3', 'Dropped']);
        } else {
            $query->where('created_by', $user->id);
        }
        return $query;
    }

    private function getRoleBasedApplicationQuery(Request $request)
    {
        $query = Application::query();
        $user = auth()->user();
        $this->applyDateFiltersToQuery($query, $request);

        if ($request->filled('user_id') && $user->hasRole(['Administrator', 'Applications Manager', 'Manager'])) {
            $query->where('created_by', $request->user_id);
        }

        if (!$user->hasRole(['Administrator', 'Applications Manager', 'Manager'])) {
            $query->where(fn($q) => $q->where('user_id', $user->id)->orWhere('created_by', $user->id));
        }
        return $query;
    }

    private function getLeaderboard(Request $request)
    {
        if (!auth()->user()->hasRole(['Administrator', 'Leads Manager', 'Manager'])) return [];

        $query = Lead::query();
        $this->applyDateFiltersToQuery($query, $request);

        return $query->select('created_by', DB::raw('count(*) as total_leads'), DB::raw('SUM(CASE WHEN is_forwarded = true THEN 1 ELSE 0 END) as converted_leads'))
                     ->with('creator:id,name,last')->whereNotNull('created_by')->groupBy('created_by')
                     ->orderBy('converted_leads', 'desc')->limit(10)->get()
                     ->map(function ($item) {
                        $item->conversion_rate = $item->total_leads > 0 ? round(($item->converted_leads / $item->total_leads) * 100, 1) : 0;
                        return $item;
                     });
    }
    
    private function calculateAvgConversionTime($query)
    {
        $avgSeconds = $query->where('is_forwarded', true)->whereNotNull('forwarded_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, created_at, forwarded_at)) as avg_time'))->value('avg_time');
        return $avgSeconds ? round($avgSeconds / 86400, 1) : 0;
    }

    private function getApplicationStatusDistribution($query)
    {
        $droppedCount = $query->clone()->whereHas('lead', fn($q) => $q->where('status', 'Dropped'))->count();
        $statusDistribution = $query->clone()->select('status', DB::raw('count(*) as count'))->whereNotNull('status')->groupBy('status')->pluck('count', 'status')->toArray();
        $statusDistribution['Dropped'] = ($statusDistribution['Dropped'] ?? 0) + $droppedCount;
        return $statusDistribution;
    }
    
    private function applyDateFiltersToQuery($query, Request $request)
    {
        if ($request->filled('dateFrom')) {
            $query->whereDate('created_at', '>=', $request->dateFrom);
        }
        if ($request->filled('dateTo')) {
            $query->whereDate('created_at', '<=', $request->dateTo);
        }
        return $query;
    }
}