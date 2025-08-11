<?php
namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Form;
use App\Models\Product;
use App\Models\DataEntry;
use App\Models\LeadComment;
use App\Models\Application;
use App\Models\Upload;
use App\Models\Document;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentForwarded;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class LeadController extends Controller
{
    private $notificationController;

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
        $this->middleware('auth');
        $this->middleware('can:view_leads')->only('index', 'indexs');
        $this->middleware('can:create_leads')->only('create', 'store');
        $this->middleware('can:edit_leads')->only('storeforwardedLead');
    }
    
    /**
     * Check if the current user is an Administrator or Lead Manager
     * 
     * @return bool
     */
    private function isAdminOrLeadManager()
    {
        // Get user roles
        $userRoles = auth()->user()->roles()->pluck('name')->toArray();
        
        // Check if the user has either Administrator or Leads Manager role
        return in_array('Administrator', $userRoles) || in_array('Leads Manager', $userRoles) || in_array('Manager', $userRoles);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Gate::denies('view_leads')) {
            \Log::warning('Unauthorized attempt to view leads by user: ' . auth()->user()->id);
            abort(403, 'Unauthorized action.');
        }

        // Check if user is Admin or Lead Manager
        if ($this->isAdminOrLeadManager()) {
            // Show all leads for admins and lead managers
            $leads = Lead::where('is_forwarded', false)->get();
        } else {
            // Only retrieve leads created by the current user for standard users
            $leads = Lead::where('is_forwarded', false)
                         ->where('created_by', auth()->id())
                         ->get();
        }
        // In your controller
        $leads = Lead::with('creator')->get();
        
        // Add user variable
        $user = auth()->user();

        return view('backend.leadform.index', compact('leads', 'user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Gate::denies('create_leads')) {
            abort(403, 'Unauthorized action.');
        }

        $products = Product::all();
        $applications = Application::all();
        $users = User::all();
        $data_entries = DataEntry::all();
        $user = auth()->user();
        
        return view('backend.leadform.create', compact('products', 'data_entries', 'applications', 'user','users'));
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
{
    if (Gate::denies('create_leads')) {
        abort(403, 'Unauthorized action.');
    }
    
    // Debug information to identify the issue
    \Log::info('Lead creation request data:', $request->all());
    \Log::info('District value specifically: ' . $request->input('district'));
    
    // Validate the incoming data
    $validated = $request->validate([
        'sources' => 'nullable|string|max:255',
        'name' => 'required|string|max:255',
        'last' => 'nullable|string|max:255',
        'email' => 'nullable|string|max:255',
        'phone' => 'required|string|max:255',
        'locations' => 'nullable|string|max:255',
        'lastqualification' => 'nullable|string|max:255',
        'courselevel' => 'nullable|string|max:255',
        'passed' => 'nullable|string|max:255',
        'gpa' => 'nullable|string|max:255',
        'englishTest' => 'nullable|string|max:255',
        'academic' => 'nullable|string',
        'higher' => 'nullable|string|max:255',
        'less' => 'nullable|string|max:255',
        'score' => 'nullable|string|max:255',
        'englishscore' => 'nullable|string|max:255',
        'otherScore' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'location' => 'nullable|string|max:255',
        'university' => 'nullable|array',
        'university.*' => 'nullable|string|max:255',
        'course' => 'nullable|array',
        'course.*' => 'nullable|string|max:255',
        'intake' => 'nullable|array',
        'intake.*' => 'nullable|string|max:255',
        'offerss' => 'nullable|string|max:255',
        'englishtheory' => 'nullable|string|max:255',
        'source' => 'nullable|string|max:255',
        'partnerDetails' => 'nullable|string|max:255',
        'otherDetails' => 'nullable|string|max:255',
        'link' => 'nullable|string|max:255',
        'status' => 'nullable|string|max:255',
        'created_by' =>'required|exists:users,id',
    ]);

    // Retrieve the data from the request
    $universities = $request->input('university', []);
    $courses = $request->input('course', []);
    $intakes = $request->input('intake', []);

    // Get the count of the arrays
    $count = max(count($universities), count($courses), count($intakes));
    
    // If there are no items in the arrays, create at least one lead
    $count = $count > 0 ? $count : 1;

    // Loop through the university, course, and intake arrays and store them
    for ($i = 0; $i < $count; $i++) {
        $lead = Lead::create([
            'sources' => $request->input('sources'),
            'name' => $request->input('name'),
            'last' => $request->input('last'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'locations' => $request->input('locations'),
            'lastqualification' => $request->input('lastqualification'),
            'courselevel' => $request->input('courselevel'),
            'passed' => $request->input('passed'),
            'gpa' => $request->input('gpa'),
            'englishTest' => $request->input('englishTest'),
            'academic' => $request->input('academic'),
            'higher' => $request->input('higher'),
            'less' => $request->input('less'),
            'score' => $request->input('score'),
            'englishscore' => $request->input('englishscore'),
            'otherScore' => $request->input('otherScore'),
            'country' => $request->input('country'),
            'location' => $request->input('location'),
            'englishtheory' => $request->input('englishtheory'),
            'university' => $universities[$i] ?? null,
            'course' => $courses[$i] ?? null,
            'intake' => $intakes[$i] ?? null,
            'offerss' => $request->input('offerss'),
            'source' => $request->input('source'),
            'partnerDetails' => $request->input('partnerDetails'),
            'otherDetails' => $request->input('otherDetails'),
            'link' => $request->input('link'),
            'status' => $request->input('status'),
            'created_by' => $request->input('created_by'),
        ]);
        
        // Assign a random avatar to the lead
        $lead->assignRandomAvatar();
    }        activity()
    ->causedBy(auth()->user())
    ->performedOn($lead)
    ->withProperties([
        'action' => 'created_lead',
        'name' => $lead->name,
        'university' => $lead->university,
    ])
    ->log(' ' . auth()->user()->name . ' created a new lead');

    return redirect()->route('backend.leadform.indexs')->with('success', 'Lead saved successfully!');
}
    
    public function updateCreator(Request $request, $id)
{
    try {
        $lead = Lead::findOrFail($id);
        $lead->created_by = $request->created_by;
        $lead->save();
        
        return response()->json(['success' => true, 'message' => 'Creator updated successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}
public function storeForwardedLead(Request $request, $id, $type)
{
    \Log::info('=== STARTING storeForwardedLead METHOD ===');
    \Log::info('Lead ID: ' . $id);
    \Log::info('Type: ' . $type);
    \Log::info('Request data:', $request->all());
    \Log::info('Current user ID: ' . auth()->user()->id);
    \Log::info('Current user name: ' . auth()->user()->name);

    try {
        // Step 1: Validate the request
        \Log::info('STEP 1: Starting request validation');
        $validated = $request->validate([
            'document_id' => 'required',
            'selected_user_id' => 'required|exists:users,id',
            'selected_user_email' => 'nullable|email',
            'notes' => 'nullable|string',
            'is_forwarded' => 'required|boolean',
            'send_email' => 'nullable|in:on,off'
        ]);
        \Log::info('STEP 1: Request validation successful');
        \Log::info('Validated data:', $validated);
    } catch (\Exception $e) {
        \Log::error('STEP 1: Request validation failed: ' . $e->getMessage());
        throw $e;
    }

    // Step 2: Find the lead
    \Log::info('STEP 2: Finding lead with ID: ' . $id);
    try {
        $lead = Lead::findOrFail($id);
        \Log::info('STEP 2: Lead found successfully');
        \Log::info('Lead details:', [
            'id' => $lead->id,
            'name' => $lead->name,
            'email' => $lead->email,
            'is_forwarded' => $lead->is_forwarded,
            'forwarded_to' => $lead->forwarded_to,
            'forwarded_at' => $lead->forwarded_at
        ]);
    } catch (\Exception $e) {
        \Log::error('STEP 2: Failed to find lead: ' . $e->getMessage());
        throw $e;
    }

    // Step 3: Find the recipient user
    \Log::info('STEP 3: Finding recipient user with ID: ' . $request->selected_user_id);
    $recipient = User::find($request->selected_user_id);
    if (!$recipient) {
        \Log::error('STEP 3: Recipient user not found for ID: ' . $request->selected_user_id);
        return redirect()->back()->with('error', 'Recipient user not found.');
    }
    \Log::info('STEP 3: Recipient user found successfully');
    \Log::info('Recipient details:', [
        'id' => $recipient->id,
        'name' => $recipient->name,
        'email' => $recipient->email
    ]);

    // Step 4: Determine recipient email
    \Log::info('STEP 4: Determining recipient email');
    $recipientEmail = $request->selected_user_email ?? $recipient->email;
    \Log::info('STEP 4: Recipient email determined: ' . $recipientEmail);

    // Step 5: Update lead forwarding details
    \Log::info('STEP 5: Updating lead forwarding details');
    \Log::info('Previous lead state:', [
        'forwarded_to' => $lead->forwarded_to,
        'forwarded_notes' => $lead->forwarded_notes,
        'is_forwarded' => $lead->is_forwarded,
        'forwarded_at' => $lead->forwarded_at
    ]);

    try {
        $lead->forwarded_to = $recipient->id;
        $lead->forwarded_notes = $request->notes;
        $lead->is_forwarded = $request->is_forwarded;
        $lead->forwarded_at = now();
        $lead->save();
        \Log::info('STEP 5: Lead forwarding details updated successfully');
        \Log::info('New lead state:', [
            'forwarded_to' => $lead->forwarded_to,
            'forwarded_notes' => $lead->forwarded_notes,
            'is_forwarded' => $lead->is_forwarded,
            'forwarded_at' => $lead->forwarded_at
        ]);
    } catch (\Exception $e) {
        \Log::error('STEP 5: Failed to update lead forwarding details: ' . $e->getMessage());
        throw $e;
    }

    // Step 6: Check if we need to create application
    \Log::info('STEP 6: Checking if application creation is needed');
    \Log::info('Type check: ' . $type . ' === "lead" ? ' . ($type === 'lead' ? 'YES' : 'NO'));
    \Log::info('is_forwarded check: ' . ($request->is_forwarded ? 'YES' : 'NO'));

    if ($type === 'lead' && $request->is_forwarded) {
        \Log::info('STEP 6: Application creation is needed - proceeding');
        \Log::info("Forwarding lead with ID: " . $id . " to user: " . $recipient->name);

        // Step 7: Create new application record
        \Log::info('STEP 7: Creating new application record');
        try {
            $application = new Application();
            $applicationData = [
                'sources' => $lead->sources,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'locations' => $lead->locations,
                'lastqualification' => $lead->lastqualification,
                'courselevel' => $lead->courselevel,
                'passed' => $lead->passed,
                'gpa' => $lead->gpa,
                'englishTest' => $lead->englishTest,
                'academic' => $lead->academic,
                'higher' => $lead->higher,
                'less' => $lead->less,
                'score' => $lead->score,
                'englishscore' => $lead->englishscore,
                'otherScore' => $lead->otherScore,
                'country' => $lead->country,
                'location' => $lead->location,
                'university' => $lead->university,
                'course' => $lead->course,
                'intake' => $lead->intake,
                'offerss' => $lead->offerss,
                'englishtheory' => $lead->englishtheory,
                'source' => $lead->source,
                'otherDetails' => $lead->otherDetails,
                'link' => $lead->link,
                'notes' => $request->notes,
                'avatar' => $lead->avatar,
                'lead_id' => $lead->id,
                'created_by' => $request->selected_user_id,
            ];
            \Log::info('STEP 7: Application data prepared:', $applicationData);
            
            $application->fill($applicationData);
            $application->save();
            
            \Log::info('STEP 7: Application created successfully with ID: ' . $application->id);
        } catch (\Exception $e) {
            \Log::error('STEP 7: Failed to create application: ' . $e->getMessage());
            \Log::error('STEP 7: Application creation error trace: ' . $e->getTraceAsString());
            throw $e;
        }

        // Step 8: Mark lead as forwarded (redundant but keeping for safety)
        \Log::info('STEP 8: Marking lead as forwarded');
        try {
            $lead->is_forwarded = true;
            $lead->save();
            \Log::info('STEP 8: Lead marked as forwarded successfully');
        } catch (\Exception $e) {
            \Log::error('STEP 8: Failed to mark lead as forwarded: ' . $e->getMessage());
            throw $e;
        }

        // Step 9: Log activity
        \Log::info('STEP 9: Logging activity');
        try {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($lead)
                ->withProperties([
                    'action' => 'forwarded_lead',
                    'lead_name' => $lead->name ?? 'N/A',
                    'forwarded_to' => $recipient->name ?? 'N/A',
                    'forwarded_to_id' => $recipient->id,
                    'notes' => $request->notes,
                    'created_application' => isset($application) ? true : false,
                    'application_id' => $application->id ?? null,
                ])
                ->log(auth()->user()->name . ' forwarded lead "' . ($lead->name ?? 'N/A') . '" to "' . ($recipient->name ?? 'N/A') );
            \Log::info('STEP 9: Activity logged successfully');
        } catch (\Exception $e) {
            \Log::error('STEP 9: Failed to log activity: ' . $e->getMessage());
            // Don't throw here as this is not critical
        }

        // Step 10: Send email if requested
        \Log::info('STEP 10: Checking if email should be sent');
        \Log::info('send_email in request: ' . ($request->has('send_email') ? 'YES' : 'NO'));
        \Log::info('recipient email exists: ' . ($recipientEmail ? 'YES (' . $recipientEmail . ')' : 'NO'));
        
        if ($request->has('send_email') && $recipientEmail) {
            \Log::info('STEP 10: Attempting to send email to: ' . $recipientEmail . ' for forwarded lead: ' . $id);
            try {
                Mail::to($recipientEmail)->send(new DocumentForwarded($lead, auth()->user(), $recipient, $request->notes, $application->id));
                \Log::info('STEP 10: Email sent successfully to ' . $recipientEmail . ' for lead: ' . $id);
            } catch (\Exception $e) {
                \Log::error('STEP 10: Failed to send email for lead ' . $id . ': ' . $e->getMessage());
                \Log::error('STEP 10: Email error stack trace: ' . $e->getTraceAsString());
                return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
            }
        } else {
            \Log::info('STEP 10: Email sending skipped', [
                'shouldSendEmail' => $request->has('send_email'), 
                'userEmail' => $recipientEmail
            ]);
        }

        // Step 11: Create notification
        \Log::info('STEP 11: Creating notification');
        \Log::info('is_forwarded check for notification: ' . ($request->is_forwarded ? 'YES' : 'NO'));
        
        if ($request->is_forwarded) {
            try {
                $this->notificationController->createForwardedDocumentNotification(
                    $lead, 
                    $recipient, 
                    $request->notes,
                    isset($application) ? $application->id : null
                );
                \Log::info('STEP 11: Notification created successfully');
            } catch (\Exception $e) {
                \Log::error('STEP 11: Failed to create notification: ' . $e->getMessage());
                // Don't throw here as this is not critical
            }
        } else {
            \Log::info('STEP 11: Notification creation skipped (is_forwarded is false)');
        }

        // Step 12: Final redirect for lead type
        \Log::info('STEP 12: Redirecting to lead index with success message');
        \Log::info('=== ENDING storeForwardedLead METHOD (SUCCESS - LEAD TYPE) ===');
        
        return redirect()->route('backend.leadform.indexs')
            ->with('success', 'Lead #' . $id . ' forwarded successfully!');
    } else {
        \Log::info('STEP 6: Application creation not needed');
        \Log::info('Reason: type=' . $type . ', is_forwarded=' . ($request->is_forwarded ? 'true' : 'false'));
    }

    // Step 13: Final redirect for non-lead type
    \Log::info('STEP 13: Redirecting back with success message (non-lead type)');
    \Log::info('=== ENDING storeForwardedLead METHOD (SUCCESS - NON-LEAD TYPE) ===');
    
    return redirect()->back()->with('success', 'Document has been forwarded successfully to ' . $recipient->name);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (Gate::denies('edit_leads')) {
            abort(403, 'Unauthorized action.');
        }

        $products = Product::all();
        $user = auth()->user();
        
        // Check if user is Admin or Lead Manager
        if ($this->isAdminOrLeadManager()) {
            // Allow admins and lead managers to edit any lead
            $leads = Lead::findOrFail($id);
        } else {
            // Ensure the user can only edit their own leads
            $leads = Lead::where('created_by', auth()->id())->findOrFail($id);
        }
        
        return view('backend.leadform.update', compact('leads', 'products', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (Gate::denies('edit_leads')) {
            abort(403, 'Unauthorized action.');
        }

        // Find the lead by ID or fail, with role-based access control
        if ($this->isAdminOrLeadManager()) {
            // Allow admins and lead managers to update any lead
            $leads = Lead::findOrFail($id);
        } else {
            // Ensure other users can only update their own leads
            $leads = Lead::where('created_by', auth()->id())->findOrFail($id);
        }

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'locations' => 'required|string|max:255',
            'lastqualification' => 'required|string|max:255',
            'courselevel' => 'required|string|max:255',
            'passed' => 'required|string|max:255',
            'gpa' => 'required|string|max:255',
            'englishTest' => 'required|string|max:255',
            'academic' => 'required|string',
            'higher' => 'required|string|max:255',
            'less' => 'required|string|max:255',
            'score' => 'required|string|max:255',
            'englishscore' => 'required|string|max:255',
            'englishtheory' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'university' => 'nullable|array',
            'university.*' => 'nullable|string|max:255',
            'course' => 'nullable|array',
            'course.*' => 'nullable|string|max:255',
            'intake' => 'nullable|array',
            'intake.*' => 'nullable|string|max:255',
            'offerss' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'partnerDetails' => 'nullable|string|max:255',
            'otherDetails' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
        ]);

        try {
            // Handle academic documents
            $academicDocs = $request->input('academic');
            // If academic comes as an array, convert to comma-separated string
            if (is_array($academicDocs)) {
                $academicDocs = implode(',', array_filter($academicDocs));
            }

            // Update the lead with validated data
            $leads->update([
                'name' => $validated['name'],
                'last' => $validated['last'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'locations' => $validated['locations'],
                'lastqualification' => $validated['lastqualification'],
                'courselevel' => $validated['courselevel'],
                'passed' => $validated['passed'],
                'gpa' => $validated['gpa'],
                'englishTest' => $validated['englishTest'],
                'academic' => $academicDocs,
                'higher' => $validated['higher'],
                'less' => $validated['less'],
                'score' => $validated['score'],
                'englishscore' => $validated['englishscore'],
                'englishtheory' => $validated['englishtheory'],
                'country' => $validated['country'],
                'location' => $validated['location'],
                'university' => $validated['university'],
                'course' => $validated['course'],
                'intake' => $validated['intake'],
                'offerss' => $validated['offerss'],
                'source' => $validated['source'],
                'partnerDetails' => $validated['partnerDetails'],
                'otherDetails' => $validated['otherDetails'],
                'link' => $validated['link'],
            ]);

            return redirect()
                ->route('backend.leadform.index')
                ->with('success', 'Lead updated successfully');

        } catch (\Exception $e) {
            \Log::error('Lead update failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update lead. Please try again.');
        }
    }

    public function updateField(Request $request, $id)
{
    if (Gate::denies('edit_leads')) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action.'
        ], 403);
    }

    try {
        // Role-based access for field updates
        if ($this->isAdminOrLeadManager()) {
            // Admins and Lead Managers can update any lead
            $lead = Lead::findOrFail($id);
        } else {
            // Regular users can only update their own leads
            $lead = Lead::where('created_by', auth()->id())->findOrFail($id);
        }

        // Validate the field is in the fillable array
        if (!in_array($request->field, $lead->getFillable())) {
            return response()->json([
                'success' => false,
                'message' => 'Field is not fillable.'
            ], 400);
        }

        // Store original value before update
        $originalValue = $lead->{$request->field};

        // Update the field
        $lead->{$request->field} = $request->value;
        $lead->save();

        // Log the update activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($lead)
            ->withProperties([
                'action' => 'updated_lead',
                'field' => $request->field,
                'old_value' => $originalValue,
                'new_value' => $request->value,
                'lead_name' => $lead->name ?? 'N/A',
            ])
            ->log(auth()->user()->name . ' updated lead "' . ($lead->name ?? 'N/A') . '" ( ' . $request->field . ')');

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('Lead update failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Gate::denies('delete_leads')) {
            abort(403, 'Unauthorized action.');
        }

        // Role-based access for deletion
        if ($this->isAdminOrLeadManager()) {
            // Admins and Lead Managers can delete any lead
            $leads = Lead::findOrFail($id);
        } else {
            // Regular users can only delete their own leads
            $leads = Lead::where('created_by', auth()->id())->findOrFail($id);
        }

        // Delete the lead from the database
        $leads->delete();

        // Redirect with success message
        return redirect()->route('backend.leadform.index')->with('success', 'Lead deleted successfully');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            foreach ($rows as $row) {
                $data = [
                    'name' => $row[0] ?? null,
                    'email' => $row[1] ?? null,
                    'phone' => $row[2] ?? null,
                    'locations' => $row[3] ?? null,
                    'lastqualification' => $row[4] ?? null,
                    'courselevel' => $row[5] ?? null,
                    'passed' => $row[6] ?? null,
                    'gpa' => $row[7] ?? null,
                    'englishTest' => $row[8] ?? null,
                    'higher' => $row[9] ?? null,
                    'less' => $row[10] ?? null,
                    'score' => $row[11] ?? null,
                    'englishscore' => $row[12] ?? null,
                    'englishtheory' => $row[13] ?? null,
                    'otherScore' => $row[14] ?? null,
                    'academic' => $row[15] ?? null,
                    'country' => $row[16] ?? null,
                    'location' => $row[17] ?? null,
                    'university' => $row[18] ?? null,
                    'course' => $row[19] ?? null,
                    'intake' => $row[20] ?? null,
                    'offerss' => $row[21] ?? null,
                    'source' => $row[22] ?? null,
                    'otherDetails' => $row[23] ?? null,
                    'sources' => $row[24] ?? null,
                    'link' => $row[25] ?? null,
                ];

                // Check if a lead already exists with the same name, email, and phone
                $existingLead = Lead::where('name', $data['name'])
                    ->where('email', $data['email'])
                    ->where('phone', $data['phone'])
                    ->first();

                if ($existingLead) {
                    // Update the existing lead
                    $existingLead->update($data);
                    // We can choose to reassign avatar here or keep the existing one
                    // $existingLead->assignRandomAvatar(); // Uncomment if you want to reassign avatar
                } else {
                    // Create a new lead
                    $data['created_by'] = auth()->id();
                    $lead = Lead::create($data);
                    $lead->assignRandomAvatar();
                }
            }

            return redirect()->back()->with('success', 'Data imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    public function upload()
    {
        if (Gate::denies('create_leads')) {
            abort(403, 'Unauthorized action.');
        }
        
        $user = auth()->user();
        return view('backend.leadform.upload', compact('user'));
    }

  public function indexs()
{
    if (Gate::denies('view_leads')) {
        abort(403, 'Unauthorized action.');
    }
    
    // Get leads with their comments
    if ($this->isAdminOrLeadManager()) {
        $leads = Lead::with('creator', 'lead_comments')->get();
    } else {
        $leads = Lead::with('creator', 'lead_comments')
              ->where('created_by', auth()->id())
              ->get();
    }
    
    // Sort by most recent activity (either lead creation OR most recent comment)
    $leads = $leads->sortByDesc(function($lead) {
        $leadCreatedAt = $lead->created_at->timestamp;
        
        // Check if there are any comments
        if ($lead->lead_comments && $lead->lead_comments->isNotEmpty()) {
            $mostRecentCommentAt = $lead->lead_comments->max('created_at')->timestamp;
            // Return whichever is more recent: lead creation or latest comment
            return max($leadCreatedAt, $mostRecentCommentAt);
        }
        
        // If no comments, just return lead creation timestamp
        return $leadCreatedAt;
    })->values();
    
    $user = auth()->user();
    $users = User::all();
    $applications = Application::all();
    $status = Lead::all();
    
    return view('backend.leadform.indexs', compact('leads', 'user', 'users', 'status'));
}
    public function records($id = null)
    {
        if (Gate::denies('view_leads')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if user is Admin or Lead Manager
        if ($this->isAdminOrLeadManager()) {
            // Retrieve the specific lead if an ID is provided, otherwise set to null
            // Admins and Lead Managers can see any lead
            $lead = $id ? Lead::findOrFail($id) : null;
            
            // Fetch all leads for admins and managers
            $leads = Lead::all();
        } else {
            // Regular users can only see their own leads
            $lead = $id ? Lead::where('created_by', auth()->id())->findOrFail($id) : null;
            $leads = Lead::where('created_by', auth()->id())->get();
        }

        // Fetch comments related to this lead if lead exists, otherwise fetch all comments
        $lead_comments = $lead
            ? LeadComment::where('lead_id', $lead->id)->orderBy('created_at', 'desc')->get()
            : LeadComment::all();

        // Fetch all users and uploads
        $users = User::all();
        $uploads = Upload::all();
        $documents = Document::all();
        $user = auth()->user();

        // Return the view with the necessary data
        return view('backend.leadform.records', [
            'lead' => $lead,
            'lead_comments' => $lead_comments,
            'users' => $users,
            'uploads' => $uploads,
            'leads' => $leads,
            'user' => $user,
            'documents' => $documents,
        ]);
    }

    public function withdraw($id)
    {
        if (Gate::denies('edit_leads')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Role-based access control for withdrawing leads
            if ($this->isAdminOrLeadManager()) {
                // Admins and Lead Managers can withdraw any lead
                $lead = Lead::findOrFail($id);
            } else {
                // Regular users can only withdraw their own leads
                $lead = Lead::where('created_by', auth()->id())->findOrFail($id);
            }

            // Update the status to "Dropped"
            $lead->status = 'Dropped';
            $lead->save();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Application withdrawn successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Application withdrawn successfully');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to withdraw application: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to withdraw application');
        }
    }

    public function ui($id = null)
    {
        if (Gate::denies('view_leads')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if user is Admin or Lead Manager
        if ($this->isAdminOrLeadManager()) {
            // Admins and Lead Managers can see all leads
            if ($id) {
                $lead = Lead::findOrFail($id);
                $leads = Lead::all();
            } else {
                $lead = null;
                $leads = Lead::all();
            }
        } else {
            // Regular users can only see their own leads
            if ($id) {
                $lead = Lead::where('created_by', auth()->id())->findOrFail($id);
                $leads = Lead::where('created_by', auth()->id())->get();
            } else {
                $lead = null;
                $leads = Lead::where('created_by', auth()->id())->get();
            }
        }
        
        $users = User::all();
        $lead_comments = LeadComment::all();
        $applications = Application::all();
        $documents = Document::all();
        $user = auth()->user();

        return view('backend.leadform.ui', compact('lead', 'leads', 'users', 'lead_comments', 'applications', 'documents', 'user'));
    }
    
    
    public function saveDocumentStatus(Request $request)
{
    // Validate the request data
    $request->validate([
        'document' => 'required|string',
    ]);

    $status = $request->input('document');
    $lead = null;

    // Option 1: From session
    if (session()->has('current_lead')) {
        $lead = session('current_lead');
    }

    // Option 2: From request
    if ($request->has('lead_id')) {
        $lead = Lead::find($request->input('lead_id'));
    }

    // Option 3 (Optional): From authenticated user
    // if (!$lead && auth()->user()?->lead) {
    //     $lead = auth()->user()->lead;
    // }

    if (!$lead) {
        return response()->json(['error' => 'Lead not found. Please reload the page.'], 404);
    }

    try {
        $oldStatus = $lead->status;
        $lead->status = $status;
        $lead->save();

        $user = auth()->user();

        // Activity logging
        activity()
            ->causedBy($user)
            ->performedOn($lead)
            ->withProperties([
                'action' => 'updated_document_status',
                'performed_by' => $user->name,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'email' => $lead->email ?? 'N/A',
                'course' => $lead->course ?? 'N/A',
                'lead_name' => $lead->name ?? 'N/A',
            ])
            ->log("{$user->name} updated the document status of lead '{$lead->name}'");

        return response()->json(['message' => 'Document status saved successfully']);

    } catch (\Exception $e) {
        \Log::error('Error saving document status', [
            'error' => $e->getMessage(),
            'user' => auth()->user()?->name,
            'lead_id' => $lead->id ?? null,
        ]);

        return response()->json(['error' => 'Failed to save document status. Please try again.'], 500);
    }
}



public function convertToHot(Request $request, $id)
{
    try {
        $lead = Lead::findOrFail($id);

        // Check if it's a raw lead (e.g., source is '1')
        if ($lead->sources == '1') { // Or whatever indicates a raw lead
            $lead->sources = '0'; // Or 'converted', or null - define what a "full lead" source is
            $lead->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead converted to Hot Lead successfully.',
                    'lead' => $lead->fresh()->toArray() // Send back updated lead data if needed
                ]);
            }
            return redirect()->back()->with('success', 'Lead converted to Hot Lead successfully.');
        } else {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Lead is not a raw lead or already converted.'], 400);
            }
            return redirect()->back()->with('error', 'Lead is not a raw lead or already converted.');
        }
    } catch (\Exception $e) {
        Log::error("Error converting lead to hot: " . $e->getMessage());
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Failed to convert lead. ' . $e->getMessage()], 500);
        }
        return redirect()->back()->with('error', 'Failed to convert lead. ' . $e->getMessage());
    }
}
}