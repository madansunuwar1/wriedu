<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\Lead;
use App\Models\LeadComment;
use App\Models\DataEntry;
use App\Events\LeadUpdated;
use App\Models\User;
use App\Models\Document;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LeadApiController extends Controller
{
    private $notificationController;

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
        $this->middleware('auth');
    }

    public function spa()
    {
        $user = auth()->user();
        $users = User::select('id', 'name', 'last')->get();
        $locations = Lead::distinct()->whereNotNull('locations')->pluck('locations');
        $userRoles = $user->getRoleNames()->all();
        $userReminders = \App\Models\LeadComment::where('user_id', $user->id)
            ->where('date_time', '>=', now())
            ->with('lead:id,name')
            ->get();

        return view('spa', compact('users', 'locations', 'userRoles', 'userReminders'));
    }

    protected function leadQuery()
    {
        $query = Lead::query();
        $user = auth()->user();

        // If user is an admin or manager, they can see all leads, so no filter is applied.
        if ($this->isAdminOrApplicationManager()) {
            // No filter needed for these roles.
        }
        // If the user has the 'Front Desk (Receptionist)' role, they see only leads with specific statuses.
        elseif ($user->hasRole('Front Desk (Receptionist)')) {
            $query->whereIn('status', ['Phone Not Received 2', 'Phone Not Received 3', 'Dropped']);
        }
        // All other non-admin users can only see leads assigned to them or created by them.
        else {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('created_by', $user->id);
            });
        }

        return $query;
    }

    private function isAdminOrApplicationManager()
    {
        return auth()->user()->hasRole(['Administrator', 'Leads Manager', 'Manager']);
    }

    private function buildLeadsQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->leadQuery();

       $query->with([
            'creator:id,name,last',
            'lead_comments' => function ($q) {
                $q->latest()->limit(1);
            },
            // Add this line to eager-load the applications relationship.
            // We only select the columns needed by the accessor for maximum efficiency.
            'applications:id,lead_id,status,created_at'
        ]);

        // Apply filters
        $query->when($request->input('search'), function ($q, $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });

        $query->when($request->input('location'), fn($q, $v) => $q->where('locations', $v));

        if ($request->filled('user') && $request->input('user') !== '' && $this->isAdminOrApplicationManager()) {
            $query->where('created_by', $request->input('user'));
        }

        $query->when($request->input('leadType'), function ($q, $leadType) {
            if ($leadType === 'raw') return $q->where('sources', '1');
            if ($leadType === 'hot') return $q->where('sources', '!=', '1');
        });

        $query->when($request->input('lastQualification'), fn($q, $v) => $q->where('lastqualification', $v));
        $query->when($request->input('country'), fn($q, $v) => $q->where('country', $v));
        $query->when($request->input('status'), fn($q, $v) => $q->where('status', $v));
        $query->when($request->input('dateFrom'), fn($q, $v) => $q->whereDate('created_at', '>=', $v));
        $query->when($request->input('dateTo'), fn($q, $v) => $q->whereDate('created_at', '<=', $v));

        // Consistent ordering
        $query->orderByRaw("CASE WHEN status = 'Dropped' THEN 1 ELSE 0 END ASC")
            ->orderBy('id', 'desc');

        return $query;
    }

    private function getBaseQueryForFilters(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->leadQuery();

        if ($request->input('user') && $this->isAdminOrApplicationManager()) {
            $query->where('created_by', $request->input('user'));
        }

        return $query;
    }

    public function getLeadsApi(Request $request)
    {
        $query = $this->buildLeadsQuery($request);

        // Get pagination parameters with defaults
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Ensure page is valid
        $page = max(1, (int) $page);
        $perPage = max(1, min(100, (int) $perPage));

        $leads = $query->paginate($perPage, ['*'], 'page', $page);

        // Fetch all users
        $allUsers = User::select('id', 'name', 'last')->get();

        // Get filter options from ALL data (not just current page)
        $baseQuery = $this->getBaseQueryForFilters($request);
        $filterOptions = [
            'locations' => $baseQuery->clone()->distinct()->whereNotNull('locations')->pluck('locations')->filter()->values(),
            'statuses' => $baseQuery->clone()->distinct()->whereNotNull('status')->pluck('status')->filter()->values(),
            'countries' => $baseQuery->clone()->distinct()->whereNotNull('country')->pluck('country')->filter()->values(),
            'lastQualifications' => $baseQuery->clone()->distinct()->whereNotNull('lastqualification')->pluck('lastqualification')->filter()->values(),
        ];

        return response()->json([
            'leads' => $leads,
            'users' => $allUsers,
            'filterOptions' => $filterOptions,
        ]);
    }

    public function getLeadRecordApi(Lead $lead)
    {
        // [MODIFIED] Eager load activities with their causer
       $lead->load([
            'creator:id,name,last',
            'lead_comments.createdBy:id,name,last,avatar',
            'uploads',
            // Add 'applications' here so the accessor works on the lead record page
            'applications:id,lead_id,status,created_at',
            'activities' => function ($query) {
                $query->with('causer:id,name,last')->orderBy('created_at', 'desc');
            }
        ]);

        $allUsers = User::select('id', 'name', 'last', 'email')->get();
        $documentStatuses = Document::pluck('document');
        $formOptions = [
            'countries' => DataEntry::distinct()->pluck('country'),
            'course_levels' => ['Undergraduate', 'Postgraduate'],
            'locations' => [],
            'universities' => [],
            'courses' => [],
            'intakes' => [],
        ];

        if ($lead->country) {
            $formOptions['locations'] = DataEntry::where('country', $lead->country)->distinct()->pluck('newLocation');
        }

        if ($lead->location) {
            $formOptions['universities'] = DataEntry::where('newLocation', 'like', "%{$lead->location}%")->distinct()->pluck('newUniversity');
        }

        if ($lead->university && $lead->location) {
            $courseQuery = DataEntry::where('newUniversity', $lead->university)->where('newLocation', 'like', "%{$lead->location}%");
            if ($lead->courselevel) {
                $courseQuery->where('level', $lead->courselevel);
            }
            $formOptions['courses'] = $courseQuery->distinct()->pluck('newCourse');
        }

        if ($lead->course && $lead->university && $lead->location) {
            $formOptions['intakes'] = DataEntry::where('newCourse', $lead->course)
                ->where('newUniversity', $lead->university)
                ->where('newLocation', 'like', "%{$lead->location}%")
                ->distinct()->pluck('newIntake');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'lead' => $lead,
                'users' => $allUsers,
                'document_statuses' => $documentStatuses,
                'form_options' => $formOptions,
            ]
        ]);
    }

    public function storeCommentApi(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'date_time' => 'nullable|date',
        ]);

        try {
            $comment = LeadComment::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'comment' => $validated['comment'],
                'created_at' => $validated['date_time'] ?? now(),
                'updated_at' => $validated['date_time'] ?? now(),
            ]);

            // [ADDED] Log this activity for the timeline
            activity()
                ->causedBy(auth()->user())
                ->performedOn($lead)
                ->withProperties([ // <-- Add this block
                    'action' => 'comment_added',
                    'comment_body' => $validated['comment'],
                    'comment_id' => $comment->id
                ])
                ->log("commented");


            $lead->touch(); // Keep this to update the lead's updated_at timestamp
            $lead->load(['activities' => function ($query) {
                $query->with('causer:id,name,last')->orderBy('created_at', 'desc');
            }]);

            return response()->json([
                'success' => true,
                'message' => 'Comment saved successfully.',
                'comment' => $comment->load('createdBy:id,name,last,avatar'),
                'activities' => $lead->activities // <-- ADD THIS
            ]);
        } catch (\Exception $e) {
            Log::error('API comment store failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save comment.'], 500);
        }
    }

    public function storeApi(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|unique:leads,email',
        ]);

        if (Lead::where('phone', $validated['phone'])->where('email', $validated['email'])->exists()) {
            throw ValidationException::withMessages(['duplicate' => 'A lead with this email and phone already exists.']);
        }

        // [THE FIX] Create a new Lead object in memory, but don't save it yet.
        $lead = new Lead($validated);

        // Set the creator
        $lead->created_by = auth()->id();

        // Call the model method to set the avatar property on the object
        $lead->assignRandomAvatar();

        // Now, save the fully-prepared object to the database in ONE step.
        $lead->save();

        event(new LeadUpdated($lead));

        return response()->json(['success' => true, 'message' => 'Lead created successfully!', 'lead' => $lead], 201);
    }

    public function getCreateData()
    {
        $allUsers = \App\Models\User::select('id', 'name', 'last')->get();
        $documents = \App\Models\Document::pluck('document');
        $countries = \App\Models\DataEntry::distinct()->pluck('country');

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $allUsers,
                'documents' => $documents,
                'countries' => $countries,
            ],
        ]);
    }

    public function updateField(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'field' => 'required|string',
            'value' => 'nullable'
        ]);

        $fieldToUpdate = $validated['field'];
        $newValue = $validated['value'];

        if (!in_array($fieldToUpdate, $lead->getFillable())) {
            return response()->json(['success' => false, 'message' => 'Field is not updatable.'], 422);
        }

        // --- PRIMARY LOGIC: PREVENT STATUS CHANGES ON FORWARDED LEADS ---
        // If the lead has been forwarded, its status is now controlled by the application.
        // We block any attempt to change its status, EXCEPT for dropping it.
        if ($fieldToUpdate === 'status' && $lead->is_forwarded && $newValue !== 'Dropped') {
            return response()->json([
                'success' => false,
                'message' => "This lead's status is controlled by its application. Please update the application status instead."
            ], 403); // 403 Forbidden is an appropriate status code here.
        }

        $oldValue = $lead->{$fieldToUpdate};

        DB::beginTransaction();
        try {
            // Update the lead field
            $lead->{$fieldToUpdate} = $newValue;
            $lead->save();

            // --- SYNC LOGIC FOR 'DROPPED' STATUS ---
            // If the status was just changed to 'Dropped', sync this to all child applications.
            if ($fieldToUpdate === 'status' && $newValue === 'Dropped') {
                Application::where('lead_id', $lead->id)->update(['status' => 'Dropped']);
            }

            // ... (your logging and activity code remains the same)
            activity()
                ->causedBy(auth()->user())
                ->performedOn($lead)
                ->withProperties(['action' => 'field_updated', 'field' => $fieldToUpdate, 'old_value' => $oldValue, 'new_value' => $newValue])
                ->log("updated");

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update lead field for Lead ID {$lead->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred during the update.'], 500);
        }

        event(new LeadUpdated($lead->fresh()));

        $lead->load(['activities' => function ($query) {
            $query->with('causer:id,name,last')->orderBy('created_at', 'desc');
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Field updated successfully.',
            'activities' => $lead->activities
        ]);
    }

    public function forwardApplication(Request $request, Lead $lead)
    {
        try {
            $validated = $request->validate([
                'userId' => 'required|exists:users,id',
                'notes' => 'nullable|string',
                'sendEmail' => 'required|boolean',
            ]);

            $recipient = User::findOrFail($validated['userId']);

            DB::beginTransaction();

            try {
                $application = Application::create([
                    'sources' => $lead->sources,
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'locations' => $lead->locations,
                    'location' => $lead->location,
                    'country' => $lead->country,
                    'course' => $lead->course,
                    'university' => $lead->university,
                    'avatar' => $lead->avatar,
                    'intake' => $lead->intake,
                    'lastqualification' => $lead->lastqualification,
                    'level' => $lead->courselevel,
                    'passed' => $lead->passed,
                    'gpa' => $lead->gpa,
                    'englishTest' => $lead->englishTest,
                    'score' => $lead->score,
                    'englishscore' => $lead->englishscore,
                    'englishtheory' => $lead->englishtheory,
                    'academic' => $lead->academic,
                    'lead_id' => $lead->id,
                    'created_by' => $recipient->id,
                    'notes' => $validated['notes'],
                    'status' => 'active',
                ]);

                $lead->update([
                    'is_forwarded' => true,
                    'forwarded_to' => $recipient->id,
                    'forwarded_notes' => $validated['notes'],
                    'forwarded_at' => now(),
                ]);

                DB::commit();
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($lead)
                    ->withProperties([
                        'action' => 'application_forwarded',
                        'forwarded_to_user_name' => $recipient->name . ' ' . $recipient->last,
                        'application_id' => $application->id
                    ])
                    ->log("forwarded an application");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Database error during forwarding: " . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'DB error during forwarding.'], 500);
            }

            event(new LeadUpdated($lead->fresh()));

            if ($validated['sendEmail'] && $recipient->email) {
                try {
                    Mail::to($recipient->email)->send(new \App\Mail\DocumentForwarded($lead, auth()->user(), $recipient, $validated['notes'], $application->id));
                } catch (\Exception $e) {
                    Log::error("Failed to send email: " . $e->getMessage());
                }
            }

            try {
                $this->notificationController->createForwardedDocumentNotification($lead, $recipient, $validated['notes'], $application->id);
            } catch (\Exception $e) {
                Log::error("Failed to create notification: " . $e->getMessage());
            }

            return response()->json(['success' => true, 'message' => 'Application forwarded successfully.']);
        } catch (\Exception $e) {
            Log::error("Unexpected error during forwarding: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred.'], 500);
        }
    }

    public function exportLeadsApi(Request $request)
    {
        $query = $this->buildLeadsQuery($request);
        $leads = $query->get();

        return response()->json($leads);
    }

    public function convertToHot(Lead $lead)
    {
        if ($lead->sources !== '1') {
            return response()->json(['success' => false, 'message' => 'This is not a raw lead.'], 422);
        }

        $lead->sources = '0';
        $lead->save();
        event(new LeadUpdated($lead->fresh()));

        return response()->json(['success' => true, 'message' => 'Lead converted to Hot Lead.']);
    }

    public function importApi(Request $request)
    {
        if (!auth()->user()->hasRole(['Administrator', 'Admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate(['file' => 'required|mimes:xlsx,xls|max:10240']);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            $headerRow = array_shift($rows);
            $headerMap = array_flip(array_map('trim', $headerRow));

            $dbToExcelMap = [
                'name' => 'Name',
                'email' => 'Email',
                'phone' => 'Phone',
                'status' => 'Status',
                'locations' => 'Interested Location',
                'country' => 'Country',
                'location' => 'University',
                'course' => 'Course',
                'intake' => 'Intake',
                'courselevel' => 'Course Level',
                'lastqualification' => 'Last Qualification',
                'passed' => 'Year Passed',
                'gpa' => 'GPA / Percentage',
                'academic' => 'Academic Gaps',
                'englishTest' => 'English Test',
                'score' => 'Overall Score',
                'englishscore' => 'English Score (Listening, Reading, etc.)',
                'englishtheory' => 'English Theory',
                'forwarded_notes' => 'Forwarded Notes',
            ];

            $importedCount = 0;
            $updatedCount = 0;

            foreach ($rows as $row) {
                $data = [];

                foreach ($dbToExcelMap as $dbField => $excelHeader) {
                    if (isset($headerMap[$excelHeader]) && isset($row[$headerMap[$excelHeader]])) {
                        $data[$dbField] = trim($row[$headerMap[$excelHeader]]);
                    }
                }

                if (empty($data['name']) && empty($data['email']) && empty($data['phone'])) continue;

                $sourceColumnIndex = $headerMap['Source'] ?? null;
                $data['sources'] = ($sourceColumnIndex !== null && isset($row[$sourceColumnIndex]) && strtolower(trim($row[$sourceColumnIndex])) === 'hot lead') ? '0' : '1';

                $updateData = array_filter($data, fn($value) => $value !== null && $value !== '');

                $existingLead = !empty($data['email']) ? Lead::where('email', $data['email'])->first() : null;

                if ($existingLead) {
                    $existingLead->update($updateData);
                    $updatedCount++;
                } else {
                    $lead = new Lead($data);

                    $lead->created_by = auth()->id();

                    // Call the model method to set the avatar
                    $lead->assignRandomAvatar();

                    // Save the fully-prepared lead in ONE step
                    $lead->save();

                    $importedCount++;
                }
            }

            return response()->json(['success' => true, 'message' => "Import successful! Created: {$importedCount}, Updated: {$updatedCount}."]);
        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'An error occurred during import.'], 500);
        }
    }

    public function snoozeCommentApi(Request $request, LeadComment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'snooze_minutes' => 'required|integer|min:1',
        ]);

        $comment->date_time = now()->addMinutes($request->snooze_minutes);
        $comment->save();

        return response()->json(['success' => true, 'message' => 'Reminder snoozed.']);
    }

    public function withdraw(Lead $lead)
    {
        DB::beginTransaction();
        try {
            // Step 1: Update the lead's status to 'Dropped'
            $lead->status = 'Dropped';
            $lead->save();

            // Step 2: Since the lead is dropped, all its child applications must also be dropped.
            $updatedCount = Application::where('lead_id', $lead->id)->update(['status' => 'Dropped']);

            // ... (your logging and activity code remains the same)
            activity()
                ->causedBy(auth()->user())
                ->performedOn($lead)
                ->withProperties(['action' => 'withdrew_lead', 'synced_applications_count' => $updatedCount])
                ->log('withdrew a lead and synced related applications');

            event(new LeadUpdated($lead->fresh()));

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Lead has been withdrawn, and all related applications have been updated.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to withdraw lead ID {$lead->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while withdrawing the lead.'], 500);
        }
    }

    public function destroyApi(Lead $lead)
    {
        $lead->delete();

        return response()->json(null, 204);
    }
}
