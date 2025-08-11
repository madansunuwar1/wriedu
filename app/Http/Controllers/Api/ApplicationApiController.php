<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Lead;
use App\Models\University;
use App\Models\Product;
use App\Models\Partner;
use App\Models\Document;
use App\Models\Upload;
use App\Models\LeadComment;
use App\Models\CommentAdd;
use App\Models\User;
use App\Models\DataEntry;
use App\Models\CASFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Events\LeadUpdated;

class ApplicationApiController extends Controller
{
    // ... all other functions are unchanged ...

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('can:view_applications')->only(['index', 'record', 'comment', 'indexcomment', 'getUserApplications', 'withdraw', 'exportAll']);
        $this->middleware('can:edit_applications')->only(['edit', 'update', 'recordApplication']);
        $this->middleware('can:delete_applications')->only(['withdraw']);
    }

    /**
     * Check if current user is an administrator
     */
    protected function isAdminOrApplicationManager()
    {
        $userRoles = auth()->user()->roles()->pluck('name')->toArray();
        return in_array('Administrator', $userRoles) || in_array('Applications Manager', $userRoles) || in_array('Manager', $userRoles);
    }


    public function getUniversityDataForVue()
    {
        // Eager load relationships if you have them defined
        $data_entries = DataEntry::orderBy('newUniversity')->get();
        $universities = University::all(['name', 'image_link']);

        return response()->json([
            'data_entries' => $data_entries,
            'universities' => $universities
        ]);
    }

    public function getUniversityData()
    {
        try {
            // Fetch data from the data_entries table
            $data = DataEntry::select(
                'newUniversity',
                'newLocation',
                'newCourse',
                'newIntake',
                'newScholarship',
                'newAmount',
                'newIelts',
                'newpte',
                'newPgIelts',
                'newPgPte',
                'newug',
                'newpg',
                'newgpaug',
                'newgpapg',
                'newtest',
                'country',
                'requireddocuments',
                'level'
            )->get();

            // Return the data as a JSON response
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // Log any errors
            Log::error('Error fetching university data: ' . $e->getMessage());

            // Return an error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch university data.'
            ], 500);
        }
    }


    // In app/Http/Controllers/Api/ApplicationApiController.php

    public function studentstore(Request $request)
    {
        Log::info('Received student application submission.', ['request_data' => $request->all()]);

        try {
            // --- ADD THIS CHECK ---
            // First, let's be absolutely sure we have an authenticated user.
            // This is the most likely point of failure.
            $userId = auth()->id();
            if (!$userId) {
                // Log the error and return a clear message.
                Log::error('studentstore attempt failed: User is not authenticated.');
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed. You must be logged in to perform this action.'
                ], 401); // 401 Unauthorized is the correct status code.
            }

            // Validation remains the same.
            $validated = $request->validate([
                'original_application_id' => 'required|integer|exists:applications,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'studyLevel' => 'required|string|in:Undergraduate,Postgraduate',
                'country' => 'required|string|max:255',
                'location' => 'nullable|string|max:255',
                'intake' => 'required|string|max:255',
                'university' => 'required|string|max:255',
                'course' => 'required|string|max:255',
                'fee' => 'nullable|string|max:255',
            ]);
            $avatars = [
                'male-1.jpg',
                'male-2.jpg',
                'male-3.jpg',
                'male-4.jpg',
                'male-5.jpg',
                'male-6.jpg',
                'male-7.jpg',
                'male-8.jpg',
                'male-9.jpg',
                'female-1.jpg',
                'female-2.jpg',
                'female-3.jpg',
            ];
            // Build the data array for the new application.
            $applicationData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'level' => $validated['studyLevel'],
                'country' => $validated['country'],
                'location' => $validated['location'],
                'intake' => $validated['intake'],
                'university' => $validated['university'],
                'course' => $validated['course'],
                'fee' => $validated['fee'],
                'created_by' => $userId, // Use the variable we confirmed exists
                'user_id' => $userId,    // Use the variable we confirmed exists
                'status' => 'active',
                'avatar' => $avatars[array_rand($avatars)],
            ];

            // Create the application.
            $application = Application::create($applicationData);
            Log::info('Student application created successfully.', ['application_id' => $application->id]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($application)
                ->withProperties([
                    'action' => 'created_application',
                    'from_original_id' => $validated['original_application_id']
                ])
                ->log('created an application');

            return response()->json([
                'success' => true,
                'message' => 'Student application saved successfully!',
                'new_application_id' => $application->id
            ], 201);
        } catch (ValidationException $ve) {
            Log::warning('Validation failed for student application.', ['errors' => $ve->errors()]);
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $ve->errors()], 422);
        } catch (Exception $e) {
            Log::error('Failed to save student application.', ['error_message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'An unexpected server error occurred while creating the application.'], 500);
        }
    }

    /**
     * Base query for applications with admin check
     */
    protected function applicationQuery()
    {
        $query = Application::query();

        if (!$this->isAdminOrApplicationManager()) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhere('created_by', auth()->id());
            });
        }

        return $query;
    }

    /**
     * Base query for leads with admin check
     */
    protected function leadQuery()
    {
        return $this->isAdminOrApplicationManager()
            ? Lead::query()
            : Lead::where('created_by', auth()->id());
    }

    /**
     * Base query for applications with role-based visibility.
     */
    protected function getBaseQuery()
    {
        $query = Application::query();

        if (!$this->isAdminOrApplicationManager()) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhere('created_by', auth()->id());
            });
        }
        return $query;
    }


    // MODIFIED: This function now contains the new logic for filtering and eager loading.
    private function buildApplicationsQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        // Start with the base query which handles permissions.
        // Eager load relationships for performance and data availability.
        $query = $this->getBaseQuery()->with(['createdBy:id,name', 'lead:id,status']);

        // Apply search filter
        $query->when($request->input('search'), function ($q, $search) {
            $q->where(function ($subQ) use ($search) {
                $subQ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });

        // NEW: Advanced status filtering logic
        $query->when($request->input('status'), function ($q, $status) {
            if ($status === 'Dropped') {
                // If filtering for "Dropped", find applications where the PARENT LEAD is Dropped.
                $q->whereHas('lead', function ($leadQuery) {
                    $leadQuery->where('status', 'Dropped');
                });
            } else {
                // For any other status:
                // 1. The application's own status must match the filter.
                $q->where('applications.status', $status)
                    // 2. AND the parent lead must NOT be Dropped.
                    ->where(function ($subQuery) {
                        $subQuery->whereDoesntHave('lead') // Include apps with no lead
                            ->orWhereHas('lead', function ($leadQuery) {
                                $leadQuery->where('status', '!=', 'Dropped');
                            });
                    });
            }
        });

        // Apply standard filters
        $query->when($request->input('university'), fn($q, $v) => $q->where('university', $v));
        $query->when($request->input('course'), fn($q, $v) => $q->where('course', $v));
        $query->when($request->input('intake'), fn($q, $v) => $q->where('intake', 'like', "%{$v}%"));

        // Apply date range filter
        $query->when($request->input('dateFrom'), fn($q, $v) => $q->whereDate('created_at', '>=', $v));
        $query->when($request->input('dateTo'), fn($q, $v) => $q->whereDate('created_at', '<=', $v));

        // Apply user filter (only if admin/manager)
        if ($this->isAdminOrApplicationManager() && $request->filled('createdBy')) {
            $userId = User::where('name', $request->input('createdBy'))->value('id');
            if ($userId) {
                $query->where('created_by', $userId);
            }
        }

        // Consistent ordering
        // --- MODIFICATION HERE ---
        // This custom sorting pushes 'Dropped' applications to the bottom of the list.
        // It also prioritizes 'Visa Granted' applications above normal ones.
        $query->orderByRaw("CASE WHEN status = 'Dropped' THEN 2 WHEN status = 'Visa Granted' THEN 1 ELSE 0 END ASC")
            ->orderBy('id', 'desc'); // Secondary sort to keep newest at the top within each group

        return $query;
    }

    // MODIFIED: This function now transforms data for display and handles exports correctly.
    public function index(Request $request)
    {
        if (Gate::denies('view_applications')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $query = $this->buildApplicationsQuery($request);
        $perPage = $request->input('per_page', 10);

        // NEW: A reusable function to modify the status for display purposes.
        $statusTransformer = function ($application) {
            if ($application->lead && $application->lead->status === 'Dropped') {
                $application->status = 'Dropped';
            }
            return $application;
        };

        if ($perPage == -1) {
            // EXPORT Request: Fetch all matching records, transform them, and return as a flat array.
            $applicationsResponse = $query->get()->map($statusTransformer);
        } else {
            // PAGINATION Request: Fetch paginated records and transform only the items on the current page.
            $paginator = $query->paginate($perPage);
            $paginator->getCollection()->transform($statusTransformer);
            $applicationsResponse = $paginator;
        }

        $allUsers = User::select('id', 'name')->orderBy('name')->get();
        $baseFilterQuery = $this->getBaseQuery();
        $filterOptions = [
            'universities' => $baseFilterQuery->clone()->distinct()->whereNotNull('university')->pluck('university')->sort()->values(),
            'courses' => $baseFilterQuery->clone()->distinct()->whereNotNull('course')->pluck('course')->sort()->values(),
            'intakes' => $baseFilterQuery->clone()->distinct()->whereNotNull('intake')->pluck('intake')->sort()->values(),
            // Ensure "Dropped" is available as a filter option
            'statuses' => $baseFilterQuery->clone()->distinct()->whereNotNull('status')->pluck('status')->push('Dropped')->unique()->sort()->values(),
        ];

        return response()->json([
            'applications' => $applicationsResponse,
            'users' => $allUsers,
            'filterOptions' => $filterOptions,
        ]);
    }
    public function webIndex()
    {

        return view('spa');
    }
    public function webRecord()
    {

        return view('spa');
    }

    // MODIFIED: This function now also transforms the status to be consistent.
    public function exportAll()
    {
        if (Gate::denies('view_applications')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        try {
            // Eager-load both createdBy and lead relationships.
            $allApplications = Application::with(['createdBy:id,name', 'lead:id,status'])
                ->orderBy('id', 'desc')
                ->get();

            // NEW: Apply the same status transformation as the main index.
            $allApplications->transform(function ($application) {
                if ($application->lead && $application->lead->status === 'Dropped') {
                    $application->status = 'Dropped';
                }
                return $application;
            });

            return response()->json($allApplications);
        } catch (\Exception $e) {
            Log::error('Full application export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Could not retrieve data for full export.'], 500);
        }
    }

    public function record($id, $type = 'application')
    {
        try {
            $application = $this->applicationQuery()
                ->with(['user', 'partner'])
                ->findOrFail($id);

            $lead_comments = LeadComment::with('user')->where('application_id', $id)->get();
            $cas_feedbacks = CASFeedback::with('user')
                ->where('application_id', $id)
                ->get();

            $commentAdds = CommentAdd::all();
            $dataEntries = DataEntry::all();

            $lead = null;
            if ($application->lead_id) {
                $lead = Lead::with('user')->find($application->lead_id);
            }

            $leads = $this->leadQuery()->get();
            $documents = Document::all();
            $uploads = Upload::where('application_id', $id)->get();
            $partners = Partner::select('id', 'agency_name', 'email', 'agency_counselor_email')
                ->orderBy('agency_name')
                ->get();

            return response()->json([
                'application' => $application,
                'leads' => $leads,
                'lead' => $lead,
                'lead_comments' => $lead_comments,
                'documents' => $documents,
                'commentAdds' => $commentAdds,
                'partners' => $partners,
                'dataEntries' => $dataEntries,
                'type' => $type,
                'uploads' => $uploads,
                'cas_feedbacks' => $cas_feedbacks
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Application not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving application details: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return response()->json(['error' => 'Unable to retrieve application details.'], 500);
        }
    }

    public function comment()
    {
        if (Gate::denies('view_applications')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $commentAdds = CommentAdd::when(!$this->isAdminOrApplicationManager(), function ($query) {
            return $query->whereHas('application', function ($q) {
                $q->where('created_by', auth()->id());
            });
        })->get();

        return response()->json(compact('commentAdds'));
    }

    public function indexcomment()
    {
        if (Gate::denies('view_applications')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $comments = LeadComment::when(!$this->isAdminOrApplicationManager(), function ($query) {
            return $query->whereHas('application', function ($q) {
                $q->where('created_by', auth()->id());
            });
        })->get();

        return response()->json(compact('comments'));
    }

    /**
     * Store a newly created application in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Add authorization check
        if (Gate::denies('create_applications')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Comprehensive validation (Your validation is good)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'lastqualification' => 'required|string|max:255',
            'passed' => 'required|integer|min:1950|max:' . date('Y'),
            'gpa' => 'required|string|max:255',
            'english' => 'required|exists:products,id', // Assuming 'english' is product_id
            'englishTest' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'university' => 'required|array',
            'course' => 'required|array',
            'intake' => 'required|array',
            'university.*' => 'required|string|max:255',
            'course.*' => 'required|string|max:255',
            'intake.*' => 'required|string|max:255',
            'higher' => 'nullable|string|max:255',
            'less' => 'nullable|string|max:255',
            'score' => 'nullable|string|max:255',
            'englishscore' => 'nullable|string|max:255',
            'englishtheory' => 'nullable|string|max:255',
            'additionalinfo' => 'nullable|string|max:255',
            'source' => 'required|string|max:255',
            'partnerDetails' => 'nullable|string|max:255',
            'otherDetails' => 'nullable|string|max:255',
        ]);

        Log::info('Validation passed for new application', ['validated_data' => $validatedData]);

        try {
            DB::beginTransaction();

            $universities = $validatedData['university'];
            $courses = $validatedData['course'];
            $intakes = $validatedData['intake'];
            $count = count($universities);

            $lastApplication = null;

            // Create multiple application entries if multiple universities are provided
            for ($i = 0; $i < $count; $i++) {
                // --- CORRECTED LOGIC: Build the data array explicitly ---
                // Do NOT use the spread operator here.
                $applicationData = [
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'phone' => $validatedData['phone'],
                    'lastqualification' => $validatedData['lastqualification'],
                    'passed' => $validatedData['passed'],
                    'gpa' => $validatedData['gpa'],
                    'english' => $validatedData['english'],
                    'englishTest' => $validatedData['englishTest'],
                    'country' => $validatedData['country'],
                    'location' => $validatedData['location'],
                    'source' => $validatedData['source'],

                    // Nullable scalar fields
                    'higher' => $validatedData['higher'] ?? null,
                    'less' => $validatedData['less'] ?? null,
                    'score' => $validatedData['score'] ?? null,
                    'englishscore' => $validatedData['englishscore'] ?? null,
                    'englishtheory' => $validatedData['englishtheory'] ?? null,
                    'additionalinfo' => $validatedData['additionalinfo'] ?? 'N/A',
                    'partnerDetails' => $validatedData['partnerDetails'] ?? null,
                    'otherDetails' => $validatedData['otherDetails'] ?? null,

                    // Per-row data from the loops
                    'university' => $universities[$i],
                    'course' => $courses[$i],
                    'intake' => $intakes[$i],

                    // Securely set the creator
                    'created_by' => auth()->id(),
                    'user_id' => auth()->id(),
                ];

                $application = Application::create($applicationData);
                $application->assignRandomAvatar(); // Assuming this method exists on your model
                $lastApplication = $application;
            }

            if ($lastApplication) {
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($lastApplication)
                    ->withProperties([
                        'action' => 'created_application_batch',
                        'count' => $count,
                        'name' => $lastApplication->name,
                    ])
                    ->log('created ' . $count . ' application(s)');
            }

            DB::commit();
            Log::info('Application(s) created successfully.', ['count' => $count]);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully' // This message is key for the toast
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in application submission', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application. Please try again.' // This message is also key
            ], 500);
        }
    }


   public function import(Request $request)
{
    Log::info('Starting robust application import process');

    try {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file was uploaded.'], 400);
        }
        $file = $request->file('file');
        
        // Basic file validation
        $allowedExtensions = ['xlsx', 'csv'];
        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions)) {
            return response()->json(['error' => 'Invalid file type. Please upload a .xlsx or .csv file.'], 422);
        }

        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        $rowCount = count($rows);

        if ($rowCount <= 1) {
            Log::warning('File contains only header or is empty', ['rowCount' => $rowCount]);
            return response()->json(['error' => 'File contains no data rows to import.'], 400);
        }

        $avatars = [
            'male-1.jpg', 'male-2.jpg', 'male-3.jpg', 'male-4.jpg', 'male-5.jpg',
            'male-6.jpg', 'male-7.jpg', 'male-8.jpg', 'male-9.jpg',
            'female-1.jpg', 'female-2.jpg', 'female-3.jpg',
        ];

        $header = array_shift($rows);
        $cleanedHeaders = array_map(fn($h) => strtolower(trim($h)), $header);
        $headerCount = count($cleanedHeaders);

        // --- THIS IS THE CORRECTED LOGIC ---
        $fillableColumns = (new Application())->getFillable();
        // Create a map of lowercase fillable names to their original-cased names
        $fillableMap = [];
        foreach ($fillableColumns as $column) {
            $fillableMap[strtolower($column)] = $column;
        }
        // --- END OF CORRECTION ---

        $created = 0;
        $errors = 0;
        $errorDetails = [];

        foreach ($rows as $index => $row) {
            try {
                $rowNumber = $index + 2;
                $rowCount = count($row);

                if ($headerCount !== $rowCount) {
                    throw new Exception(
                        "Column count mismatch on row {$rowNumber}. The header has {$headerCount} columns, but this row has {$rowCount} columns. Please check the Excel file for extra data or empty columns in this row."
                    );
                }

                // Combine the lowercase headers with the row data
                $rowData = array_combine($cleanedHeaders, $row);

                // --- THIS IS THE NEW, ROBUST PROCESSING LOGIC ---
                $dataToCreate = [];
                foreach ($rowData as $lowercaseKey => $value) {
                    // Check if our lowercase key exists in the fillable map
                    if (isset($fillableMap[$lowercaseKey])) {
                        // If it does, get the original, correct-cased column name
                        $originalCaseKey = $fillableMap[$lowercaseKey];
                        // And add it to our final data array
                        $dataToCreate[$originalCaseKey] = $value;
                    }
                }
                // --- END OF NEW LOGIC ---

                if (empty($dataToCreate['name'])) {
                    throw new Exception("Row {$rowNumber} is missing the required 'name' field.");
                }
                
                $dataToCreate['created_by'] = auth()->id();
                $dataToCreate['user_id'] = auth()->id();
                $dataToCreate['avatar'] = $avatars[array_rand($avatars)];

                Application::create($dataToCreate); // Use the new, corrected array
                $created++;
                
            } catch (Exception $rowError) {
                $errors++;
                $errorDetails[] = "Row {$rowNumber}: " . $rowError->getMessage();
                Log::error("Error processing import row", [
                    'row_number' => $rowNumber,
                    'error' => $rowError->getMessage()
                ]);
            }
        }

        Log::info('Import process completed', ['created' => $created, 'errors' => $errors]);

        if ($errors > 0) {
            $message = "Import completed with errors: {$created} applications created, {$errors} rows failed.";
            return response()->json(['success' => false, 'message' => $message, 'errors' => $errorDetails], 422);
        }
        
        $message = "Import successful! {$created} new applications were imported.";
        return response()->json(['success' => true, 'message' => $message]);

    } catch (Exception $e) {
        Log::error('Fatal error during import process', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['error' => 'An unexpected error occurred during import: ' . $e->getMessage()], 500);
    }
}

    public function edit($id)
    {
        if (Gate::denies('edit_applications')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $comment = LeadComment::when(!$this->isAdminOrApplicationManager(), function ($query) {
            return $query->whereHas('application', function ($q) {
                $q->where('created_by', auth()->id());
            });
        })->findOrFail($id);

        $commentAdds = CommentAdd::when(!$this->isAdminOrApplicationManager(), function ($query) {
            return $query->whereHas('application', function ($q) {
                $q->where('created_by', auth()->id());
            });
        })->get();

        return response()->json(compact('comment', 'commentAdds'));
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('edit_applications')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $lead_comments = LeadComment::when(!$this->isAdminOrApplicationManager(), function ($query) {
            return $query->whereHas('application', function ($q) {
                $q->where('created_by', auth()->id());
            });
        })->findOrFail($id);

        $validated = $request->validate([
            'application' => 'required|string|max:255',
            'comment' => 'required|string|max:255',
        ]);

        $lead_comments->update($validated);

        return response()->json(['success' => true, 'message' => 'Comment updated successfully']);
    }

    public function getUserApplications($userId)
    {
        $applications = Application::where('created_by', $userId)->get();
        return response()->json(compact('applications'));
    }

    public function updateSingleField(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'field' => 'required|string',
                'value' => 'required|string',
                'applications_id' => 'required|exists:applications,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $application = Application::findOrFail($request->applications_id);

            if (!in_array($request->field, $application->getFillable())) {
                throw new \Exception('Invalid field name');
            }

            $originalValue = $application->{$request->field};
            $application->{$request->field} = $request->value;
            $application->save();

            activity()
                ->causedBy(auth()->user())
                ->performedOn($application)
                ->withProperties([
                    'action' => 'updated_application',
                    'field' => $request->field,
                    'old_value' => $originalValue,
                    'new_value' => $request->value,
                ])
                ->log('updated an application');

            return response()->json(['success' => true, 'message' => 'Field updated successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function withdraw(Request $request, $id = null)
    {
        // This method handles withdrawing a SINGLE application
        if ($id) {
            if (Gate::denies('delete_applications')) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            DB::beginTransaction();
            try {
                $application = $this->applicationQuery()->with('lead')->findOrFail($id);

                // This is a 'Dropped' action, which is bi-directional.
                // We call the `saveDocumentStatus` method to handle the sync logic.
                // This keeps our logic centralized and clean.
                $syncRequest = new Request(['document' => 'Dropped', 'application_id' => $id]);
                $response = $this->saveDocumentStatus($syncRequest);

                // Check if the sync was successful before committing
                if ($response->getStatusCode() !== 200) {
                    throw new \Exception('Failed to sync status during withdrawal.');
                }

                // Log the specific 'withdraw' action
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($application)
                    ->withProperties(['action' => 'withdrew_application', 'lead_id' => $application->lead_id])
                    ->log('withdrew an application');

                DB::commit();

                return response()->json(['success' => true, 'message' => 'Application has been withdrawn successfully.']);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to withdraw application ID {$id}: " . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'An error occurred while withdrawing the application.'], 500);
            }
        }

        // Fallback for any other use
        $applications = $this->applicationQuery()->get();
        return response()->json(compact('applications'));
    }

    public function recordApplication(Request $request, $id, $type = 'application')
    {
        if (Gate::denies('create_applications')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'document_id' => 'required',
            'selected_user_id' => 'required',
            'notes' => 'nullable|string'
        ]);

        $application = $this->applicationQuery()->findOrFail($id);

        return response()->json(['success' => true, 'message' => 'Application submitted successfully']);
    }

    public function updateField(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $validatedData = $request->validate([
            'field' => 'required|string',
            'value' => 'nullable',
        ]);

        $field = $validatedData['field'];
        $value = $validatedData['value'];

        // THE FIX: This logic is now much cleaner.
        // We just check if the field is in the fillable array.
        if (in_array($field, $application->getFillable())) {
            $application->{$field} = $value;
        } else {
            return response()->json(['success' => false, 'message' => "Field '{$field}' cannot be updated."], 400);
        }

        $application->save();

        // This is still important! It ensures the full partner object is sent back.
        $application->load('partner');

        return response()->json(['success' => true, 'application' => $application]);
    }

    public function saveDocumentStatus(Request $request)
    {
        $request->validate([
            'document' => 'required|string',
            'application_id' => 'required|exists:applications,id',
        ]);

        $applicationId = $request->input('application_id');
        $newStatus = $request->input('document');

        // Eager load the lead relationship to avoid extra queries
        $application = Application::with('lead')->find($applicationId);

        if (!$application) {
            return response()->json(['error' => 'Application not found.'], 404);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $application->status;
            $application->status = $newStatus;
            $application->save();

            // --- PRIMARY LOGIC: SYNC APPLICATION STATUS TO PARENT LEAD ---
            // If this application has a parent lead, the lead's status MUST match the application's status.
            if ($application->lead) {
                $lead = $application->lead;
                $lead->status = $newStatus; // The application is the source of truth.
                $lead->save();

                // Notify the Lead dashboard in real-time
                event(new LeadUpdated($lead->fresh()));
            }

            // ... (your logging and activity code remains the same)
            $user = auth()->user();
            activity()
                ->causedBy($user)
                ->performedOn($application)
                ->withProperties([
                    'action' => 'updated_document_status',
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ])
                ->log("updated the document status of application '{$application->name}'");

            DB::commit();

            return response()->json([
                'message' => 'Document status saved successfully and parent lead has been updated.',
                'status' => $application->status,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving document status', ['application_id' => $applicationId, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to save document status.'], 500);
        }
    }
}