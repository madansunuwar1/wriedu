<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\LeadComment;
use App\Models\Upload;
use App\Models\Partner;
use App\Models\CommentAdd;
use App\Models\Document;
use App\Models\DataEntry;
use App\Models\Lead;
use App\Models\CASFeedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ApplicationHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view_applications')->only(['index', 'record', 'comment', 'indexcomment', 'getUserApplications', 'withdraw']);
        $this->middleware('can:edit_applications')->only(['edit', 'update', 'recordApplication']);
        $this->middleware('can:delete_applications')->only(['withdraw']);
        
    }

    /**
     * Check if current user is an administrator
     */
    protected function isAdminOrApplicationManager()
    {
        // Get user roles
        $userRoles = auth()->user()->roles()->pluck('name')->toArray();
        
        // Check if the user has either Administrator or Application Manager role
        return in_array('Administrator', $userRoles) || in_array('Applications Manager', $userRoles) || in_array('Manager', $userRoles);
    }

    /**
     * Base query for applications with admin check
     */
  protected function applicationQuery()
{
    $query = Application::query();

    // If user is not an admin or application manager, show only assigned or created by them
    if (!$this->isAdminOrApplicationManager()) {
        $query->where(function($q) {
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

    public function index()
    {
        if (Gate::denies('view_applications')) {
            abort(403, 'Unauthorized action.');
        }

        $applications = $this->applicationQuery()
            ->with(['createdBy'])
            ->get();

        $documents = Document::all();
        $leads = $this->leadQuery()->get();
         $users = User::orderBy('name')->get();

        return view('backend.application.index', compact('applications', 'documents', 'leads','users'));
    }
public function record($id, $type = 'application')
{
    try {
        // Fetch the application based on ID and eager load the user and partner relationships
        $application = $this->applicationQuery()
            ->with(['user', 'partner'])
            ->findOrFail($id);
       
        // Fetch lead comments for this specific application
        $lead_comments = LeadComment::where('application_id', $id)->get();
        
        // Fetch CAS feedbacks for this specific application
        $cas_feedbacks = CASFeedback::with('user')
            ->where('application_id', $id)
            ->get();
        
        // Get comments based on role - keeping original logic but fixing the structure
        if ($this->isAdminOrApplicationManager()) {
            $commentAdds = CommentAdd::all();
            $dataEntries = DataEntry::all();
        } else {
            // Note: This overwrites lead_comments - verify if this is intended
            $lead_comments = LeadComment::all();
            $commentAdds = CommentAdd::all();
            $dataEntries = DataEntry::all();
        }
        
        // Get the specific lead for this application using the lead_id from application
        $lead = null;
if ($application->lead_id) {
    $lead = Lead::with('user')->find($application->lead_id);
}

        
        // Get all leads (if you need them for some other purpose)
        $leads = $this->leadQuery()->get();
        
        // Get documents - adjust based on your actual relationship structure
        $documents = Document::all(); // Keep as is if documents aren't tied to specific applications
        
        // Get uploads - adjust based on your actual relationship structure  
        $uploads = Upload::all(); // Keep as is if uploads aren't tied to specific applications
        
        // Get partners with only necessary fields for better performance
        $partners = Partner::select('id', 'agency_name', 'email', 'agency_counselor_email')
            ->orderBy('agency_name')
            ->get();
            
        return view('backend.application.record', [
            'application' => $application,
            'leads' => $leads,
            'lead' => $lead, // Now this is a single model instance, not a collection
            'lead_comments' => $lead_comments,
            'documents' => $documents,
            'commentAdds' => $commentAdds,
            'partners' => $partners,
            'data_entries' => $dataEntries,
            'type' => $type,
            'uploads' => $uploads,
            'cas_feedbacks' => $cas_feedbacks
        ]);
    } catch (ModelNotFoundException $e) {
        return redirect()->back()->with('error', 'Application not found.');
    } catch (\Exception $e) {
        logger()->error('Error retrieving application details: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Unable to retrieve application details.');
    }
}


    public function comment()
    {
        if (Gate::denies('view_applications')) {
            abort(403, 'Unauthorized action.');
        }

        $commentAdds = CommentAdd::when(!$this->isAdminOrApplicationManager(), function($query) {
            return $query->whereHas('application', function($q) {
                $q->where('created_by', auth()->id());
            });
        })->get();

        return view('backend.application.comment', compact('commentAdds'));
    }

    public function indexcomment()
    {
        if (Gate::denies('view_applications')) {
            abort(403, 'Unauthorized action.');
        }

        $comments = LeadComment::when(!$this->isAdminOrApplicationManager(), function($query) {
            return $query->whereHas('application', function($q) {
                $q->where('created_by', auth()->id());
            });
        })->get();

        return view('backend.application.indexcomment', compact('comments'));
    }

   public function store(Request $request)
{
    $request->validate([
        'application_id' => 'required|exists:applications,id',
        'comment' => 'required|string|max:255',
    ]);

    // Ensure the application exists and is accessible (optional business logic)
    $application = $this->applicationQuery()
        ->where('id', $request->application_id)
        ->firstOrFail();

    // Create the comment securely
    LeadComment::create([
        'application_id' => $request->application_id,
        'comment' => $request->comment,
        'user_id' => auth()->id(), // assuming this is the foreign key
        'comment_type' => 'general', // or dynamic if needed
    ]);

    
    return redirect()->route('backend.application.record', [
    'id' => $request->application_id,])->with('success', 'Comment added successfully!');
}

public function import(Request $request)
{
    Log::info('Starting application import process');
    
    Log::info('Validating uploaded file');
    
    // Manual validation to better handle errors
    try {
        if (!$request->hasFile('file')) {
            Log::error('No file was uploaded');
            return redirect()->back()->with('error', 'No file was uploaded');
        }
        
        $file = $request->file('file');
        
        // Log file details before validation
        Log::info('File received', [
            'originalName' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension()
        ]);
        
        // Validate file type manually for better logging
        $allowedTypes = ['xlsx', 'xls', 'csv'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedTypes)) {
            Log::error('Invalid file type', ['extension' => $extension]);
            return redirect()->back()->with('error', 'Invalid file type. Please upload a valid Excel or CSV file.');
        }
        
        // Validate file size manually (10MB max)
        $maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if ($file->getSize() > $maxSize) {
            Log::error('File size exceeds maximum', ['size' => $file->getSize(), 'maxSize' => $maxSize]);
            return redirect()->back()->with('error', 'File size exceeds the maximum limit of 10MB.');
        }
        
        Log::info('File validation passed successfully');
        
        try {
            // Load and process spreadsheet
            Log::info('Loading spreadsheet with IOFactory', ['path' => $file->getPathname()]);
            $spreadsheet = IOFactory::load($file->getPathname());
            Log::info('Spreadsheet loaded successfully');
            
            $worksheet = $spreadsheet->getActiveSheet();
            Log::info('Active worksheet retrieved');
            
            $rows = $worksheet->toArray();
            $rowCount = count($rows);
            Log::info("Raw data retrieved. Total rows: {$rowCount}");
            
            if ($rowCount <= 1) {
                Log::warning('File contains only header or is empty', ['rowCount' => $rowCount]);
                return redirect()->back()->with('warning', 'File contains no data rows to import');
            }
            
            // Remove header row
            array_shift($rows);
            $totalRows = count($rows);
            Log::info("Processing {$totalRows} data rows (header removed)");
            
            $created = 0;
            $updated = 0;
            $errors = 0;
            
            foreach ($rows as $index => $row) {
                try {
                    Log::debug("Processing row " . ($index + 1), [
                        'name' => $row[0] ?? 'N/A',
                        'email' => $row[1] ?? 'N/A'
                    ]);
                    
                    $data = [
                        'name' => $row[0] ?? null,
                        'email' => $row[1] ?? null,
                        'phone' => $row[2] ?? null,
                        'location' => $row[3] ?? null,
                        'lastqualification' => $row[4] ?? null,
                        'course' => $row[5] ?? null,
                        'passed' => $row[6] ?? null,
                        'gpa' => $row[7] ?? null,
                        'english' => $row[8] ?? null,
                        'englishTest' => $row[9] ?? null,
                        'higher' => $row[10] ?? null,
                        'less' => $row[11] ?? null,
                        'score' => $row[12] ?? null,
                        'englishscore' => $row[13] ?? null,
                        'englishtheory' => $row[14] ?? null,
                        'document' => $row[15] ?? null,
                        'country' => $row[16] ?? null,
                        'university' => $row[17] ?? null,
                        'intake' => $row[18] ?? null,
                        'additionalinfo' => $row[19] ?? null,
                        'source' => $row[20] ?? null,
                        'otherDetails' => $row[21] ?? null,
                        'partnerDetails' => $row[22] ?? null,
                        'searchField' => $row[23] ?? null,
                        'customSearchField' => $row[24] ?? null,
                        'courseSearchField' => $row[25] ?? null,
                    ];
                    
                    // Check if application already exists using unique combination
                    $existingApplication = Application::where('name', $data['name'])
                        ->where('email', $data['email'])
                        ->where('phone', $data['phone'])
                        ->first();
                    
                    if ($existingApplication) {
                        Log::info("Updating existing application", [
                            'id' => $existingApplication->id,
                            'name' => $data['name'],
                            'email' => $data['email']
                        ]);
                        
                        $existingApplication->update($data);
                        $updated++;
                    } else {
                        Log::info("Creating new application", [
                            'name' => $data['name'],
                            'email' => $data['email']
                        ]);
                        
                        Application::create($data);
                        $created++;
                    }
                } catch (Exception $rowError) {
                    $errors++;
                    Log::error("Error processing row " . ($index + 1), [
                        'error' => $rowError->getMessage(),
                        'name' => $row[0] ?? null,
                        'email' => $row[1] ?? null,
                        'trace' => $rowError->getTraceAsString()
                    ]);
                    // Continue processing other rows
                }
            }
            
            Log::info('Import process completed', [
                'created' => $created,
                'updated' => $updated,
                'errors' => $errors,
                'total_processed' => $created + $updated + $errors
            ]);
            
            return redirect()->back()->with('success', "Import completed: {$created} created, {$updated} updated, {$errors} errors.");
            
        } catch (Exception $e) {
            Log::error('Error processing spreadsheet', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error processing spreadsheet: ' . $e->getMessage());
        }
        
    } catch (ValidationException $e) {
        // Handle Laravel validation exceptions
        Log::error('Validation exception', [
            'error' => $e->getMessage(),
            'errors' => $e->errors(),
        ]);
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (Exception $e) {
        // Catch any other exceptions during validation
        Log::error('Exception during file validation', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()->with('error', 'Error validating file: ' . $e->getMessage());
    }
}

    public function edit($id)
    {
        if (Gate::denies('edit_applications')) {
            abort(403, 'Unauthorized action.');
        }

        $comment = LeadComment::when(!$this->isAdminOrApplicationManager(), function($query) {
            return $query->whereHas('application', function($q) {
                $q->where('created_by', auth()->id());
            });
        })->findOrFail($id);

        $commentAdds = CommentAdd::when(!$this->isAdminOrApplicationManager(), function($query) {
            return $query->whereHas('application', function($q) {
                $q->where('created_by', auth()->id());
            });
        })->get();

        return view('backend.application.updatecommentts', compact('comment', 'commentAdds'));
    }

    public function update(Request $request, $id)
    {
        if (Gate::denies('edit_applications')) {
            abort(403, 'Unauthorized action.');
        }

        $lead_comments = LeadComment::when(!$this->isAdminOrApplicationManager(), function($query) {
            return $query->whereHas('application', function($q) {
                $q->where('created_by', auth()->id());
            });
        })->findOrFail($id);

        $validated = $request->validate([
            'application' => 'required|string|max:255',
            'comment' => 'required|string|max:255',
        ]);
        $comment->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully'
            ]);
        }

        return redirect()->route('backend.application.indexcomment')->with('success', 'Comment updated successfully');
    }

    public function getUserApplications($userId)
    {
        $applications = Application::where('created_by', $userId)->get();
        return response()->json(['applications' => $applications]);
    }
    
    
    public function updateSingleField(Request $request)
    {
        try {
            // Validate the request
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

            // Find the form
            $applications = Appliction::findOrFail($request->applications_id);

            // Check if the field exists in the validation rules
            if (!array_key_exists($request->field, $this->getValidationRules())) {
                throw new \Exception('Invalid field name');
            }

            // Update the field
            $form->{$request->field} = $request->value;
            $form->save();

            return response()->json([
                'success' => true,
                'message' => 'Field updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function withdraw(Request $request, $id = null)
    {
        if ($id) {
            if (Gate::denies('delete_applications')) {
                abort(403, 'Unauthorized action.');
            }

            $application = $this->applicationQuery()->findOrFail($id);
            $application->status = 'Dropped';
            $application->save();
            return redirect()->back()->with('success', 'Application has been withdrawn.');
        }

        $applications = $this->applicationQuery()->get();

        return view('backend.application.withdraw', compact('applications'));
    }

    public function recordApplication(Request $request, $id, $type = 'application')
    {
        if (Gate::denies('create_applications')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'document_id' => 'required',
            'selected_user_id' => 'required',
            'notes' => 'nullable|string'
        ]);

        $application = $this->applicationQuery()->findOrFail($id);

        return redirect()->back()->with('success', 'Application submitted successfully');
    }
  
public function updateField(Request $request, $id)
{
    \Log::info('Request data:', $request->all());

    try {
        // Find the specific application by ID
        $application = Application::findOrFail($id);

        // Validation
        $validatedData = $request->validate([
            'field' => 'nullable|string',
            'value' => 'nullable|string', // Adjust the validation rules as needed
            'partnerDetails' => 'nullable|exists:partners,id',
        ]);

        // Check if the field is fillable
        if (!in_array($request->field, $application->getFillable())) {
            return response()->json([
                'success' => false,
                'message' => 'Field is not fillable'
            ], 403);
        }

        // Store original value for logging (optional)
        $originalValue = $application->{$request->field};

        // Update the field
        $application->{$request->field} = $request->value;
        $application->save();

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($application)
            ->withProperties([
                'action' => 'updated_application',
                'field' => $request->field,
                'old_value' => $originalValue,
                'new_value' => $request->value,
                'application_name' => $application->name ?? 'N/A',
            ])
            ->log(' ' . auth()->user()->name . ' updated application "' . ($application->name ?? 'N/A') . '" ( ' . $request->field . ')');

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('Application update failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}






    
   public function saveDocumentStatus(Request $request)
{
    $request->validate([
        'document' => 'required|string',
        'application_id' => 'required|exists:applications,id',
    ]);

    $applicationId = $request->input('application_id');
    $status = $request->input('document');

    $application = Application::find($applicationId);

    if (!$application) {
        Log::warning("Application not found", ['application_id' => $applicationId]);
        return response()->json(['error' => 'Application not found. Please check the Application ID.'], 404);
    }

    try {
        $oldStatus = $application->status;
        $application->status = $status;
        $application->save();

        $user = auth()->user();

        // Activity log with user name and application name
        activity()
            ->causedBy($user)
            ->performedOn($application)
            ->withProperties([
                'action' => 'updated_document_status',
                'performed_by' => $user->name,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'email' => $application->email,
                'course' => $application->course,
                'application_name' => $application->name ?? 'N/A',
            ])
            ->log("{$user->name} updated the document status of application '{$application->name}'");

        // Log info with same details
        Log::info("Document status updated", [
            'performed_by' => $user->name,
            'application_id' => $application->id,
            'application_name' => $application->name ?? 'N/A',
            'old_status' => $oldStatus,
            'new_status' => $status,
        ]);

        return response()->json([
            'message' => 'Document status saved successfully',
            'status' => $application->status,
        ]);

    } catch (\Exception $e) {
        Log::error('Error saving document status', [
            'application_id' => $applicationId,
            'error' => $e->getMessage(),
            'performed_by' => auth()->user()?->name,
        ]);

        return response()->json(['error' => 'Failed to save document status. Please try again.'], 500);
    }
}

}
