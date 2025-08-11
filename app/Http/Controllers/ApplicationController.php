<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Partner;
use App\Models\Document;
use App\Models\Upload;
use App\Models\LeadComment;
use App\Models\CommentAdd;
use App\Models\User;
use App\Models\DataEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function studentapplication()
    {
        $applications = Application::all();
        return view('backend.application.studentapplication', compact('applications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function sample()
    {

        $data_entries = DataEntry::all();
        return view('backend.application.sample', compact('data_entries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function studentstore(Request $request)
    {
        // Step 1: Log the incoming request data
        Log::info('Received student application submission.', ['request_data' => $request->all()]);

        try {
            // Step 2: Validate the incoming data
            Log::info('Starting validation for student application.');
            $validated = $request->validate([
                'original_application_id' => 'required|integer|exists:applications,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'document' => 'required|string|in:Undergraduate,Postgraduate',
                'country' => 'required|string|max:255',
                'intake' => 'required|string|max:255',
                'university' => 'required|string|max:255',
                'partnerDetails' => 'nullable|string|max:255',
                'course' => 'required|string|max:255',
                'created_by' => 'required|exists:users,id', // or 'numeric'
            ]);
            Log::info('Validation passed.', ['validated_data' => $validated]);

            // Step 3: Create the application
            $application = Application::create($validated);
            Log::info('Student application created successfully.', ['application_id' => $application->id]);
            activity()
                ->causedBy(auth()->user())
                ->performedOn($application)
                ->withProperties([
                    'action' => 'created_application',
                    'email' => $application->email,
                    'course' => $application->course,
                ])
                ->log('User created a new application');
            // Step 4: Redirect with success message
            return redirect()->route('backend.application.index')
                ->with('success', 'Student application saved successfully!');
        } catch (\Illuminate\Validation\ValidationException $ve) {
            // Log validation errors
            Log::warning('Validation failed for student application.', [
                'errors' => $ve->validator->errors()->all()
            ]);
            throw $ve; // Let Laravel handle the redirect with error messages
        } catch (\Exception $e) {
            // Log any other errors
            Log::error('Failed to save student application.', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'An unexpected error occurred while saving the application.');
        }
    }


    public function recordApplication(Request $request, $id, $type)
    {
        // Validate input
        $validated = $request->validate([
            'document_id' => 'required',
            'selected_user_id' => 'required',
            'notes' => 'nullable|string'
        ]);

        // Process the application record
        // ...

        return redirect()->back()->with('success', 'Application submitted successfully');
    }


    public function show($id)
    {
        // Retrieve the application by ID
        $application = Application::findOrFail($id);  // This will throw a 404 error if the application is not found
        $leads = Lead::all();
        $documents = Document::all();
        $lead_comments = LeadComment::all();
        $commentAdds = CommentAdd::all();
        $users = User::all();
        $uploads = Upload::all();
        // Return the view and pass the application data to it
        return view('backend.application.record', compact('application', 'leads', 'documents', 'lead_comments', 'users', 'commentAdds', 'uploads'));
    }



    /*form section start here */


    public function index()
    {

        $applications = Application::all();
        return view('backend.form.index', compact('applications'));
    }


    public function create()
    {

        $partners = Partner::all();
        $applications = Application::all();
        $products = Product::all();
        $data_entries = DataEntry::all();
        return view('backend.form.create', compact('applications', 'products', 'data_entries', 'partners'));
    }


    public function store(Request $request)
    {
        // Comprehensive validation
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'lastqualification' => 'required|string|max:255',
            'passed' => 'required|string|max:255',
            'gpa' => 'required|string|max:255',
            'english' => 'required|string|max:255',
            'englishTest' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'university' => 'required|array',
            'course' => 'required|array',
            'intake' => 'required|array',
            'university.*' => 'nullable|string|max:255',
            'course.*' => 'nullable|string|max:255',
            'intake.*' => 'nullable|string|max:255',
            'higher' => 'nullable|string|max:255',
            'less' => 'nullable|string|max:255',
            'score' => 'nullable|string|max:255',
            'englishscore' => 'nullable|string|max:255',
            'englishtheory' => 'nullable|string|max:255',
            'document' => 'required|string|max:255',
            'additionalinfo' => 'nullable|string|max:255',
            'source' => 'required|string|max:255',
            'partnerDetails' => 'nullable|string|max:255',
            'otherDetails' => 'nullable|string|max:255',
            'created_by' => 'required|exists:users,id',
        ]);

        Log::info('Validation passed', ['validated_data' => $validatedData]);

        try {
            // Get the arrays for university, course, and intake
            $universities = $request->input('university', []);
            $courses = $request->input('course', []);
            $intakes = $request->input('intake', []);

            // Determine the number of entries
            $count = max(
                count($universities),
                count($courses),
                count($intakes)
            );



            // Create multiple application entries if multiple universities/courses
            for ($i = 0; $i < $count; $i++) {
                $applicationData = [
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'phone' => $validatedData['phone'],
                    'lastqualification' => $validatedData['lastqualification'],
                    'passed' => $validatedData['passed'],
                    'gpa' => $validatedData['gpa'],
                    'english' => $validatedData['english'],
                    'englishTest' => $validatedData['englishTest'],
                    'higher' => $validatedData['higher'] ?? null,
                    'less' => $validatedData['less'] ?? null,
                    'score' => $validatedData['score'] ?? null,
                    'englishscore' => $validatedData['englishscore'] ?? null,
                    'englishtheory' => $validatedData['englishtheory'] ?? null,
                    'country' => $validatedData['country'] ?? null,
                    'location' => $validatedData['location'],
                    'university' => $universities[$i] ?? null,
                    'course' => $courses[$i] ?? null,
                    'intake' => $intakes[$i] ?? null,
                    'document' => $validatedData['document'],
                    'additionalinfo' => $validatedData['additionalinfo'] ?? null,
                    'source' => $validatedData['source'],
                    'partnerDetails' => $validatedData['partnerDetails'] ?? null,
                    'otherDetails' => $validatedData['otherDetails'] ?? null,
                    'created_by' => $validatedData['created_by'] ?? null,
                ];

                // Filter out null values if needed
                $applicationData = array_filter($applicationData, function ($value) {
                    return $value !== null;
                });

                $application = Application::create($applicationData);
                $application->assignRandomAvatar();
            }
            activity()
                ->causedBy(auth()->user())
                ->performedOn($application)
                ->withProperties([
                    'action' => 'created_application',
                    'name' => $application->name,
                    'university' => $application->university,
                ])
                ->log('User created a new application');


            Log::info('Applications created successfully');

            return redirect()->route('backend.application.index')
                ->with('success', 'Application submitted successfully');
        } catch (\Exception $e) {
            Log::error('Error in application submission', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to submit application. Please try again.')
                ->withInput();
        }
    }


    /**
     * Handle single field updates via AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
}
