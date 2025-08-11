<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Enquiry;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    private function isEmptyOrNA($value) {
        if ($value === null) return true;
        $trimmedValue = trim((string) $value);
        return $trimmedValue === '' || strtolower($trimmedValue) === 'n/a' || $trimmedValue === '-';
    }

    public function filterData(Request $request)
    {
        $range = $request->input('range');
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');
        $type = $request->input('type', 'overall');
        $filterStartDate = null; $filterEndDate = null;

        try {
             if ($range) {
                 switch ($range) {
                     case 'today': $filterStartDate = Carbon::today(); $filterEndDate = Carbon::today()->endOfDay(); break;
                     case 'week': $filterStartDate = Carbon::now()->startOfWeek(); $filterEndDate = Carbon::now()->endOfWeek(); break;
                     case 'month': $filterStartDate = Carbon::now()->startOfMonth(); $filterEndDate = Carbon::now()->endOfMonth(); break;
                     case 'year': $filterStartDate = Carbon::now()->startOfYear(); $filterEndDate = Carbon::now()->endOfYear(); break;
                     default: $filterStartDate = Carbon::now()->startOfWeek(); $filterEndDate = Carbon::now()->endOfWeek();
                 }
             } elseif ($startDateInput && $endDateInput) {
                 $filterStartDate = Carbon::parse($startDateInput)->startOfDay();
                 $filterEndDate = Carbon::parse($endDateInput)->endOfDay();
                 if ($filterEndDate->lessThan($filterStartDate)) { return response()->json(['error' => 'End date cannot be earlier than start date.'], 400); }
             } else {
                 $filterStartDate = Carbon::now()->startOfWeek(); $filterEndDate = Carbon::now()->endOfWeek();
                 $range = 'week';
             }

            $appQueryBase = Application::query()->whereBetween('created_at', [$filterStartDate, $filterEndDate]);
            $leadQueryBase = Lead::query()->whereBetween('created_at', [$filterStartDate, $filterEndDate]);
            $enquiryQueryBase = Enquiry::query()->whereBetween('created_at', [$filterStartDate, $filterEndDate]);

            $totalApplications = (clone $appQueryBase)->count();
            $totalEnquiries = (clone $enquiryQueryBase)->count();

            $allLeadsInRange = (clone $leadQueryBase)->select([
                'id', 'name', 'email', 'phone', 'lastqualification', 'englishTest',
                'university', 'country', 'intake', 'course'
            ])->get();

            $appEmails = Application::whereBetween('created_at', [$filterStartDate, $filterEndDate])
                ->whereNotNull('email')->where('email', '!=', '')->distinct()
                ->pluck('email')->map(fn($email) => strtolower(trim($email)))->flip();
            $appPhones = Application::whereBetween('created_at', [$filterStartDate, $filterEndDate])
                ->whereNotNull('phone')->where('phone', '!=', '')->distinct()
                ->pluck('phone')->map(fn($phone) => trim($phone))->flip();
            $appNames = Application::whereBetween('created_at', [$filterStartDate, $filterEndDate])
                ->whereNotNull('name')->where('name', '!=', '')->distinct()
                ->pluck('name')->map(fn($name) => strtolower(trim($name)))->flip();

            $hotLeadCount = 0; $rawLeadCount = 0; $convertedLeadCount = 0;
            $hotLeadIds = []; $rawLeadIds = [];

            foreach ($allLeadsInRange as $lead) {
                 $isHot = !$this->isEmptyOrNA($lead->lastqualification) &&
                            !$this->isEmptyOrNA($lead->englishTest) &&
                            !$this->isEmptyOrNA($lead->university) &&
                            !$this->isEmptyOrNA($lead->country) &&
                            !$this->isEmptyOrNA($lead->intake);

                 if ($isHot) {
                     $hotLeadCount++;
                     $hotLeadIds[] = $lead->id;
                 } else {
                     $hasBasicInfo = !$this->isEmptyOrNA($lead->name) &&
                                     !$this->isEmptyOrNA($lead->email) &&
                                     !$this->isEmptyOrNA($lead->phone);
                     $lacksHotInfo = $this->isEmptyOrNA($lead->lastqualification) &&
                                    $this->isEmptyOrNA($lead->englishTest) &&
                                    $this->isEmptyOrNA($lead->university) &&
                                    $this->isEmptyOrNA($lead->country) &&
                                    $this->isEmptyOrNA($lead->intake);

                     if ($hasBasicInfo && $lacksHotInfo) {
                         $rawLeadCount++;
                         $rawLeadIds[] = $lead->id;
                     }
                 }

                 $leadEmailLower = strtolower(trim($lead->email ?? ''));
                 $leadPhoneTrimmed = trim($lead->phone ?? '');
                 $leadNameLower = strtolower(trim($lead->name ?? ''));
                 $isConverted = false;
                 if (!empty($leadEmailLower) && isset($appEmails[$leadEmailLower])) { $isConverted = true; }
                 elseif (!empty($leadPhoneTrimmed) && isset($appPhones[$leadPhoneTrimmed])) { $isConverted = true; }
                 elseif (!empty($leadNameLower) && isset($appNames[$leadNameLower])) { $isConverted = true; }

                 if ($isConverted) { $convertedLeadCount++; }
            }
            $totalLeads = $allLeadsInRange->count();
            $conversionRate = ($totalLeads > 0) ? round(($convertedLeadCount / $totalLeads) * 100) : 0;

            $leadQueryForAnalytics = clone $leadQueryBase;
            if ($type === 'hotlead') { $leadQueryForAnalytics->whereIn('id', $hotLeadIds); }
            elseif ($type === 'rawlead') { $leadQueryForAnalytics->whereIn('id', $rawLeadIds); }

            $responseData = [
                 'viewType' => $type, 'applications' => $totalApplications, 'leads' => $totalLeads, 'enquiries' => $totalEnquiries,
                 'hotLeads' => $hotLeadCount, 'rawLeads' => $rawLeadCount, 'conversionRate' => $conversionRate,
                 'filterRange' => $range, 'filterStartDate' => $filterStartDate->toDateString(), 'filterEndDate' => $filterEndDate->toDateString(),
                 'trendData' => ['labels' => [], 'applications' => [], 'leads' => [], 'enquiries' => []],
                 'leadStatusData' => [], 'universityData' => [], 'leadSourceData' => [],
                 'applicationStatusData' => [], 'topCoursesData' => [], 'topAppAssigneesData' => [],
                 'topAppIntakesData' => [], 'topAppLocationsData' => [], 'topAppSourcesData' => [],
                 'topLeadCoursesData' => [], 'topLeadCreatorsData' => [], 'topLeadIntakesData' => [],
                 'topLeadCountriesData' => [], 'topLeadUniversitiesData' => [],
                 'enquiryStatusData' => [], 'enquirySourceData' => [], 'topEnquiryCountriesData' => [], 'topEnquiryEducationData' => [],
            ];

            $generateTrendData = function (Builder $queryBuilder, Carbon $startDate, Carbon $endDate, $alias = 'count') {
                 $diffInDays = $startDate->diffInDays($endDate); if ($diffInDays < 1) $diffInDays = 1;
                 $groupByExpression = null; $dateFormat = ''; $labelFormat = ''; $intervalSpec = '';
                 if ($diffInDays <= 60) { $dateFormat = 'Y-m-d'; $groupByExpression = DB::raw('DATE(created_at)'); $labelFormat = 'M d'; $intervalSpec = 'P1D'; }
                 else { $dateFormat = 'Y-m'; $groupByExpression = DB::raw("DATE_FORMAT(created_at, '%Y-%m')"); $labelFormat = 'M Y'; $intervalSpec = 'P1M'; }
                 try {
                     $grammar = $queryBuilder->getQuery()->getConnection()->getQueryGrammar(); $groupBySql = $groupByExpression->getValue($grammar);
                     $results = $queryBuilder->getQuery()->selectRaw("{$groupBySql} as period, COUNT(*) as {$alias}")->groupBy('period')->orderBy('period')->pluck($alias, 'period')->all();
                 } catch (\Exception $e) { Log::error("Trend data error: ".$e->getMessage()); return ['labels' => [], 'data' => []]; }
                 $periodData = []; $periodLabels = []; $interval = new DateInterval($intervalSpec);
                 $endDateForLoop = ($diffInDays <= 60) ? $endDate->copy()->addDay() : $endDate->copy()->endOfMonth()->addDay();
                 $periodIterator = new \Carbon\CarbonPeriod($startDate->copy()->startOfDay(), $interval, $endDateForLoop);
                 foreach ($periodIterator as $date) { if ($date->isAfter($endDate)) break; $currentPeriodKey = $date->format($dateFormat); $periodLabels[] = $date->format($labelFormat); $periodData[] = $results[$currentPeriodKey] ?? 0; }
                 return ['labels' => $periodLabels, 'data' => $periodData];
            };

            $appTrendResult = $generateTrendData(clone $appQueryBase, $filterStartDate, $filterEndDate);
            $leadTrendResult = $generateTrendData(clone $leadQueryBase, $filterStartDate, $filterEndDate);
            $enquiryTrendResult = $generateTrendData(clone $enquiryQueryBase, $filterStartDate, $filterEndDate);
            $trendLabels = $appTrendResult['labels'] ?? [];
            if (isset($leadTrendResult['labels']) && count($leadTrendResult['labels']) > count($trendLabels)) $trendLabels = $leadTrendResult['labels'];
            if (isset($enquiryTrendResult['labels']) && count($enquiryTrendResult['labels']) > count($trendLabels)) $trendLabels = $enquiryTrendResult['labels'];
            $labelCount = count($trendLabels);
            $responseData['trendData']['labels'] = $trendLabels;
            $responseData['trendData']['applications'] = array_pad($appTrendResult['data'] ?? [], $labelCount, 0);
            $responseData['trendData']['leads'] = array_pad($leadTrendResult['data'] ?? [], $labelCount, 0);
            $responseData['trendData']['enquiries'] = array_pad($enquiryTrendResult['data'] ?? [], $labelCount, 0);

            switch ($type) {
                case 'overall':
                    $responseData['leadStatusData'] = $this->getLeadStatusData(clone $leadQueryBase);
                    $responseData['universityData'] = $this->getTopData(clone $appQueryBase, 'university', 'label', 5, 'Unknown University');
                    $responseData['leadSourceData'] = $this->getTopData(clone $leadQueryBase, 'source', 'label', 5, 'Unknown Source');
                    $responseData['topCoursesData'] = $this->getTopData(clone $appQueryBase, 'course', 'label', 5, 'Unknown Course');
                    $responseData['applicationStatusData'] = $this->getApplicationStatusData(clone $appQueryBase);
                    $responseData['enquiryStatusData'] = $this->getEnquiryStatusData(clone $enquiryQueryBase);
                    break;

                case 'application':
                     $responseData['applicationStatusData'] = $this->getApplicationStatusData(clone $appQueryBase);
                     $responseData['topCoursesData'] = $this->getTopData(clone $appQueryBase, 'course', 'label', 5, 'Unknown Course');
                     $responseData['topAppAssigneesData'] = $this->getTopApplicationAssigneesData(clone $appQueryBase, 5);
                     $responseData['universityData'] = $this->getTopData(clone $appQueryBase, 'university', 'label', 5, 'Unknown University');
                     if (Schema::hasColumn('applications', 'intake')) {
                        $responseData['topAppIntakesData'] = $this->getTopData(clone $appQueryBase, 'intake', 'label', 5, 'Unknown Intake');
                     }
                     if (Schema::hasColumn('applications', 'location')) {
                        $responseData['topAppLocationsData'] = $this->getTopData(clone $appQueryBase, 'location', 'label', 5, 'Unknown Location');
                     }
                     if (Schema::hasColumn('applications', 'source')) {
                         $responseData['topAppSourcesData'] = $this->getTopData(clone $appQueryBase, 'source', 'label', 5, 'Unknown Source');
                     }
                    break;

                case 'lead':
                case 'hotlead':
                case 'rawlead':
                    $responseData['leadStatusData'] = $this->getLeadStatusData(clone $leadQueryForAnalytics);
                    $responseData['leadSourceData'] = $this->getTopData(clone $leadQueryForAnalytics, 'source', 'label', 5, 'Unknown Source');
                    $responseData['topLeadCoursesData'] = $this->getTopData(clone $leadQueryForAnalytics, 'course', 'label', 5, 'Unknown Course');
                    $responseData['topLeadCreatorsData'] = $this->getTopLeadCreatorsData(clone $leadQueryForAnalytics, 5);
                     if (Schema::hasColumn('leads', 'intake')) {
                         $responseData['topLeadIntakesData'] = $this->getTopData(clone $leadQueryForAnalytics, 'intake', 'label', 5, 'Unknown Intake');
                     }
                     if (Schema::hasColumn('leads', 'country')) {
                         $responseData['topLeadCountriesData'] = $this->getTopData(clone $leadQueryForAnalytics, 'country', 'label', 5, 'Unknown Country');
                     }
                     if (Schema::hasColumn('leads', 'university')) {
                         $responseData['topLeadUniversitiesData'] = $this->getTopData(clone $leadQueryForAnalytics, 'university', 'label', 5, 'Unknown University');
                     }
                    break;

                case 'enquiry':
                     $responseData['enquiryStatusData'] = $this->getEnquiryStatusData(clone $enquiryQueryBase);
                     if (Schema::hasColumn('enquiries', 'source')) {
                         $responseData['enquirySourceData'] = $this->getTopData(clone $enquiryQueryBase, 'source', 'label', 5, 'Unknown Source');
                     } else {
                         $responseData['enquirySourceData'] = [['label' => 'N/A (No Field)', 'count' => 0]];
                         Log::warning("Dashboard: 'source' column missing in enquiries table, skipping chart data.");
                     }
                     if (Schema::hasColumn('enquiries', 'country')) {
                        $responseData['topEnquiryCountriesData'] = $this->getTopData(clone $enquiryQueryBase, 'country', 'label', 5, 'Unknown Country');
                     }
                     if (Schema::hasColumn('enquiries', 'education')) {
                         $responseData['topEnquiryEducationData'] = $this->getTopData(clone $enquiryQueryBase, 'education', 'label', 5, 'Unknown Education');
                     }
                    break;
            }

            return response()->json($responseData);

        } catch (Exception $e) {
            Log::error("Error in filterData: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            return response()->json(['error' => 'An error occurred processing dashboard data.'], 500);
        }
    }

    private function getTopData(Builder $query, string $column, string $labelKey = 'label', int $limit = 5, $fallback = 'Unknown') {
        try {
            $model = $query->getModel();
            $tableName = $model->getTable();
            if (!Schema::hasColumn($tableName, $column)) {
                Log::warning("Dashboard: Column '{$column}' missing in table '{$tableName}'.");
                return [[$labelKey => 'N/A (No Field)', 'count' => 0]];
            }
            $dataQuery = clone $query;
            $data = $dataQuery->select($column, DB::raw('COUNT(*) as count'))
                              ->whereNotNull($column)
                              ->where(DB::raw("TRIM(COALESCE({$column}, ''))"), '!=', '')
                              ->groupBy($column)
                              ->orderBy('count', 'desc')
                              ->limit($limit)
                              ->get()
                              ->map(function ($item) use ($column, $labelKey, $fallback, $limit) { // $limit added here
                                  $labelValue = trim($item->{$column});
                                  if (empty($labelValue) && $labelValue !== '0') {
                                      $labelValue = $fallback;
                                  }
                                  $maxLength = $limit >= 10 ? 20 : ($limit >= 7 ? 25 : 35);
                                  $displayLabel = mb_strlen($labelValue) > $maxLength ? mb_substr($labelValue, 0, $maxLength - 3) . '...' : $labelValue;
                                  return [$labelKey => $displayLabel, 'count' => (int)($item->count ?? 0)];
                              })
                              ->filter(fn($item) => $item['count'] > 0)
                              ->values()
                              ->toArray();

            if (empty($data)) return [[$labelKey => 'No Data', 'count' => 0]];
            return $data;
        } catch (\Exception $e) {
            Log::error("Error fetching Top Data for '{$column}' in table '{$tableName}': ".$e->getMessage());
            return [[$labelKey => 'Error', 'count' => 0]];
        }
    }

    private function getLeadStatusData(Builder $query) {
        if (!Schema::hasColumn('leads', 'status')) {
            Log::warning("Dashboard: 'status' column missing in leads table.");
            return [['label' => 'N/A (No Field)', 'count' => 0]];
        }
        return $this->getTopData($query, 'status', 'label', 10, 'Unknown Status');
    }

   private function getApplicationStatusData(Builder $query) {
    if (!Schema::hasColumn('applications', 'status')) {
        Log::warning("Dashboard: 'status' column missing in applications table (used for status).");
        return [['label' => 'N/A (No Field)', 'count' => 0]];
    }
    
    // Call a new function that doesn't limit the results
    return $this->getAllData($query, 'status', 'label', 'Unknown Document Status');
}

// New function to get all data without a limit
private function getAllData(Builder $query, string $column, string $labelKey = 'label', $fallback = 'Unknown') {
    try {
        $model = $query->getModel();
        $tableName = $model->getTable();
        if (!Schema::hasColumn($tableName, $column)) {
            Log::warning("Dashboard: Column '{$column}' missing in table '{$tableName}'.");
            return [[$labelKey => 'N/A (No Field)', 'count' => 0]];
        }
        $dataQuery = clone $query;
        $data = $dataQuery->select($column, DB::raw('COUNT(*) as count'))
                          ->whereNotNull($column)
                          ->where(DB::raw("TRIM(COALESCE({$column}, ''))"), '!=', '')
                          ->groupBy($column)
                          ->orderBy('count', 'desc')
                          // No limit here
                          ->get()
                          ->map(function ($item) use ($column, $labelKey, $fallback) {
                              $labelValue = trim($item->{$column});
                              if (empty($labelValue) && $labelValue !== '0') {
                                  $labelValue = $fallback;
                              }
                              // Display full label without truncation
                              return [$labelKey => $labelValue, 'count' => (int)($item->count ?? 0)];
                          })
                          ->filter(fn($item) => $item['count'] > 0)
                          ->values()
                          ->toArray();

        if (empty($data)) return [[$labelKey => 'No Data', 'count' => 0]];
        return $data;
    } catch (\Exception $e) {
        Log::error("Error fetching All Data for '{$column}' in table '{$tableName}': ".$e->getMessage());
        return [[$labelKey => 'Error', 'count' => 0]];
    }
}

    private function getEnquiryStatusData(Builder $query) {
        if (!Schema::hasColumn('enquiries', 'status')) {
            Log::warning("Dashboard: 'status' column missing in enquiries table.");
            return [['label' => 'N/A (No Field)', 'count' => 0]];
        }
         return $this->getTopData($query, 'status', 'label', 10, 'Unknown Status');
    }

    private function getTopLeadCreatorsData(Builder $query, int $limit = 5) {
        if (!Schema::hasColumn('leads', 'created_by')) {
            Log::warning("Dashboard: 'created_by' column missing in leads table.");
            return [['label' => 'N/A (No Field)', 'count' => 0]];
        }
        try {
            $dataQuery = clone $query;
            $data = $dataQuery->select('created_by', DB::raw('COUNT(*) as count'))
                              ->whereNotNull('created_by')
                              ->groupBy('created_by')
                              ->orderBy('count', 'desc')
                              ->limit($limit)
                              ->when(method_exists(Lead::class, 'creator'), function ($q) {
                                  $q->with('creator:id,name,last');
                              })
                              ->get()
                              ->map(function ($item) {
                                  $creatorName = 'Unknown User';
                                  if ($item->relationLoaded('creator') && $item->creator) {
                                      $name = trim($item->creator->name . ' ' . $item->creator->last);
                                      if(!empty($name)) { $creatorName = $name; }
                                      else { $creatorName = 'User #' . $item->created_by; }
                                  } elseif ($item->created_by) {
                                     $creatorName = 'User #' . $item->created_by;
                                  }
                                  return ['label' => $creatorName, 'count' => (int)$item->count];
                              })
                              ->filter(fn($item) => $item['count'] > 0)
                              ->values()
                              ->toArray();

            if (empty($data)) return [['label' => 'No Data', 'count' => 0]];
            return $data;
        } catch (\Exception $e) {
            Log::error("Error fetching Top Lead Creators: ".$e->getMessage());
            return [['label' => 'Error', 'count' => 0]];
        }
    }

    private function getTopApplicationAssigneesData(Builder $query, int $limit = 5) {
         if (!Schema::hasColumn('applications', 'user_id')) {
            Log::warning("Dashboard: 'user_id' column missing in applications table.");
            return [['label' => 'N/A (No Field)', 'count' => 0]];
         }
         try {
            $dataQuery = clone $query;
            $data = $dataQuery->select('user_id', DB::raw('COUNT(*) as count'))
                              ->whereNotNull('user_id')
                              ->groupBy('user_id')
                              ->orderBy('count', 'desc')
                              ->limit($limit)
                              ->when(method_exists(Application::class, 'assignedUser'), function ($q) {
                                  $q->with('assignedUser:id,name,last');
                              })
                              ->get()
                              ->map(function ($item) {
                                  $assigneeName = 'Unassigned / Unknown';
                                  if ($item->user_id) {
                                      if ($item->relationLoaded('assignedUser') && $item->assignedUser) {
                                          $name = trim($item->assignedUser->name . ' ' . $item->assignedUser->last);
                                          if(!empty($name)) { $assigneeName = $name; }
                                          else { $assigneeName = 'User #' . $item->user_id; }
                                      } else {
                                          $assigneeName = 'User #' . $item->user_id;
                                      }
                                  }
                                  return ['label' => $assigneeName, 'count' => (int)$item->count];
                              })
                              ->filter(fn($item) => $item['count'] > 0)
                              ->values()
                              ->toArray();

            if (empty($data)) return [['label' => 'No Data', 'count' => 0]];
            return $data;
         } catch (\Exception $e) {
            Log::error("Error fetching Top Application Assignees: ".$e->getMessage());
            return [['label' => 'Error', 'count' => 0]];
         }
    }
}