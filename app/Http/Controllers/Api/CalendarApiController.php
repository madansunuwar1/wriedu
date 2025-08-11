<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\Notice;
use Illuminate\Support\Facades\Log;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;

class CalendarApiController extends Controller
{
    /**
     * Get initial calendar data for the current month.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $today = now()->format('Y-m-d');

        // Get Nepali date in format Y-m-d
        $nepaliDate = LaravelNepaliDate::from($today)->toNepaliDate();
        [$year, $month, $day] = explode('-', $nepaliDate);

        $calendarData = $this->generateCalendarData((int) $year, (int) $month);

        // --- FIX ---
        // The response now directly returns the calendar data object.
        // This makes it consistent with getMonthData() and matches what the Vue component expects.
        return response()->json($calendarData);
    }

    /**
     * Get calendar data for a specific month and year.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMonthData(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        $calendarData = $this->generateCalendarData($year, $month);

        return response()->json($calendarData);
    }

    private function generateCalendarData($nepaliYear, $nepaliMonth)
    {
        $totalDays = LaravelNepaliDate::daysInMonth($nepaliMonth, $nepaliYear);
        $nepaliDateStr = "{$nepaliYear}-" . str_pad($nepaliMonth, 2, '0', STR_PAD_LEFT) . "-01";
        $firstDayEnglishStr = LaravelNepaliDate::from($nepaliDateStr)->toEnglishDate();
        $firstDayCarbon = Carbon::parse($firstDayEnglishStr);
        $startDayOfWeek = $firstDayCarbon->dayOfWeek; // 0 for Sunday, 6 for Saturday

        $calendar = [];
        $dayCounter = 1;

        for ($week = 0; $week < 6; $week++) {
            $weekDays = [];

            for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
                if (($week === 0 && $dayOfWeek < $startDayOfWeek) || $dayCounter > $totalDays) {
                    $weekDays[] = null;
                } else {
                    $nepDate = "{$nepaliYear}-" . str_pad($nepaliMonth, 2, '0', STR_PAD_LEFT) . "-" . str_pad($dayCounter, 2, '0', STR_PAD_LEFT);
                    $englishDateStr = LaravelNepaliDate::from($nepDate)->toEnglishDate();

                    $weekDays[] = [
                        'nepali_date' => $dayCounter,
                        'english_date' => $englishDateStr,
                        'nepali_full_date' => $nepDate,
                        'is_today' => $this->isToday($englishDateStr),
                        'events' => $this->getEventsForDate($englishDateStr)
                    ];

                    $dayCounter++;
                }
            }

            $calendar[] = $weekDays;

            if ($dayCounter > $totalDays) {
                break;
            }
        }

        $nepaliYearText = $this->convertToNepaliNumerals($nepaliYear);
        $firstDayEnglish = Carbon::parse($firstDayEnglishStr);
        $lastDayNepali = "{$nepaliYear}-" . str_pad($nepaliMonth, 2, '0', STR_PAD_LEFT) . "-" . str_pad($totalDays, 2, '0', STR_PAD_LEFT);
        $lastDayEnglish = Carbon::parse(LaravelNepaliDate::from($lastDayNepali)->toEnglishDate());

        $englishMonthYear = $this->getEnglishMonthYear($firstDayEnglish, $lastDayEnglish);
        $combinedMonthYear = $nepaliYearText . ' ' . $this->getNepaliMonthName($nepaliMonth) . ' | ' . $englishMonthYear;

        return [
            'calendar' => $calendar,
            'current_month' => $combinedMonthYear,
            'current_year' => (int) $nepaliYear,
            'month_number' => (int) $nepaliMonth,
            'day_names' => $this->getNepaliDayNames(),
            'nepali_month_year' => $nepaliYearText . ' ' . $this->getNepaliMonthName($nepaliMonth)
        ];
    }

    /**
     * Get all events for a specific English date.
     * This now correctly finds multi-day events.
     *
     * @param string $date
     * @return \Illuminate\Support\Collection
     */
    private function getEventsForDate($date)
    {
        // --- FIX ---
        // This query now correctly fetches events that span across the given date.
        // It handles both multi-day events and single-day events (where end_date might be null).
        return Event::where(function ($query) use ($date) {
            // Case 1: Multi-day event. The given date is within the start and end dates.
            $query->where('start_date', '<=', $date)
                  ->where('end_date', '>=', $date);
        })->orWhere(function ($query) use ($date) {
            // Case 2: Single-day event. The start_date matches and end_date is null.
            $query->where('start_date', '=', $date)
                  ->whereNull('end_date');
        })
        ->with('notice')
        ->get()
        ->map(function($event) {
            $imageUrl = $event->notice && $event->notice->image
                ? asset('storage/' . $event->notice->image)
                : null;

            return [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start_date' => $event->start_date, // Added for modal
                'end_date' => $event->end_date,     // Added for modal
                'color' => $event->color,
                'type' => $event->type,
                'image_url' => $imageUrl,
                'notice' => $event->notice ? [
                    'id' => $event->notice->id,
                    'title' => $event->notice->title,
                    'description' => $event->notice->description,
                    'image_url' => $imageUrl,
                ] : null,
            ];
        });
    }
    
    // ... all other methods remain the same ...

    private function convertToNepaliNumerals($number)
    {
        $englishNumerals = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $nepaliNumerals = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];
        return str_replace($englishNumerals, $nepaliNumerals, (string) $number);
    }

    private function getEnglishMonthYear($startDate, $endDate)
    {
        $startMonth = $startDate->format('M');
        $endMonth = $endDate->format('M');
        $startYear = $startDate->format('Y');
        $endYear = $endDate->format('Y');

        if ($startMonth === $endMonth && $startYear === $endYear) {
            return $startMonth . ' ' . $startYear;
        } elseif ($startYear === $endYear) {
            return $startMonth . '/' . $endMonth . ' ' . $startYear;
        } else {
            return $startMonth . ' ' . $startYear . '/' . $endMonth . ' ' . $endYear;
        }
    }

    private function isToday($englishDateStr)
    {
        return $englishDateStr === now()->format('Y-m-d');
    }

    private function getNepaliMonthName($monthNumber)
    {
        $monthNames = [1 => 'बैशाख', 2 => 'जेठ', 3 => 'आषाढ', 4 => 'श्रावण', 5 => 'भाद्र', 6 => 'आश्विन', 7 => 'कार्तिक', 8 => 'मंसिर', 9 => 'पौष', 10 => 'माघ', 11 => 'फाल्गुन', 12 => 'चैत्र'];
        return $monthNames[$monthNumber] ?? '';
    }

    private function getNepaliDayNames()
    {
        return ['आइतबार', 'सोमबार', 'मंगलबार', 'बुधबार', 'बिहीबार', 'शुक्रबार', 'शनिबार'];
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'color' => 'nullable|string'
        ]);

        Event::create($request->all());

        return response()->json(['success' => true, 'message' => 'Event created successfully.']);
    }

    public function getNoticeDetails($noticeId)
    {
        try {
            $notice = Notice::find($noticeId);
            if (!$notice) {
                return response()->json(['error' => 'Notice not found'], 404);
            }
            return response()->json([
                'id' => $notice->id,
                'title' => $notice->title,
                'description' => $notice->description,
                'image_url' => $notice->image ? asset('storage/' . $notice->image) : null,
                'type' => $notice->type ?? 'general',
                'created_at' => $notice->created_at,
                'updated_at' => $notice->updated_at,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch notice details'], 500);
        }
    }

    public function convertAdToBs(Request $request)
    {
        try {
            $request->validate(['ad_date' => 'required|date']);
            $englishDate = $request->input('ad_date');
            Log::info('AD to BS conversion requested for date: ' . $englishDate);
            $nepaliDate = LaravelNepaliDate::from($englishDate)->toNepaliDate();
            Log::info('Successfully converted AD date ' . $englishDate . ' to BS date: ' . $nepaliDate);
            return response()->json(['converted_date' => $nepaliDate]);
        } catch (\Exception $e) {
            Log::error('AD to BS conversion error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during conversion.'], 500);
        }
    }

    public function convertBsToAd(Request $request)
    {
        try {
            $request->validate(['bs_date' => 'required|string|regex:/^\d{4}-\d{2}-\d{2}$/']);
            $nepaliDate = $request->input('bs_date');
            Log::info('BS to AD conversion requested for date: ' . $nepaliDate);
            [$bsYear, $bsMonth, $bsDay] = explode('-', $nepaliDate);
            $bsYear = (int) $bsYear;
            $bsMonth = (int) $bsMonth;
            $bsDay = (int) $bsDay;

            if ($bsYear < 1970 || $bsYear > 2100) {
                return response()->json(['error' => 'The BS year must be between 1970 and 2100.'], 400);
            }
            if ($bsMonth < 1 || $bsMonth > 12) {
                return response()->json(['error' => 'The BS month must be between 1 and 12.'], 400);
            }

            $maxDaysInMonth = LaravelNepaliDate::daysInMonth($bsMonth, $bsYear);
            if ($bsDay < 1 || $bsDay > $maxDaysInMonth) {
                $monthName = $this->getNepaliMonthName($bsMonth);
                return response()->json(['error' => "The day {$bsDay} is invalid for {$monthName} {$bsYear}. This month has only {$maxDaysInMonth} days."], 400);
            }

            $englishDate = LaravelNepaliDate::from($nepaliDate)->toEnglishDate();
            Log::info('Successfully converted BS date ' . $nepaliDate . ' to AD date: ' . $englishDate);
            return response()->json(['converted_date' => $englishDate]);
        } catch (\InvalidArgumentException $e) {
            Log::error('BS to AD conversion - Invalid date: ' . $e->getMessage());
            return response()->json(['error' => 'The provided BS date is invalid. Please check the date.'], 400);
        } catch (\Exception $e) {
            Log::error('BS to AD conversion error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during conversion. Please try again.'], 500);
        }
    }

    public function getBsMonthInfo(Request $request)
    {
        try {
            $request->validate(['year' => 'required|integer|min:1970|max:2100', 'month' => 'required|integer|min:1|max:12']);
            $year = $request->input('year');
            $month = $request->input('month');
            $maxDays = LaravelNepaliDate::daysInMonth($month, $year);
            $monthName = $this->getNepaliMonthName($month);
            return response()->json(['year' => $year, 'month' => $month, 'month_name' => $monthName, 'max_days' => $maxDays]);
        } catch (\Exception $e) {
            Log::error('BS month info error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get month information.'], 500);
        }
    }
}