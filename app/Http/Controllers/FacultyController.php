<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class FacultyController extends Controller
{

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $faculties = Faculty::all();
        return view('backend.content.index', compact('faculties', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.content.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'wantToStudy' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'courselevel' => 'required|string|max:255',
            'lastqualifications' => 'required|string|max:255',
            'passed' => 'required|string|max:255',
            'englishTest' => 'required|string|max:255',
            
        ]);

        // Store the data in the database
        Faculty::create($request->all());

        // Redirect back with success message
        return redirect()->route('backend.content.index')->with('success', 'Faculty saved successfully!');
    }


    public function storeForwardedfaculty(Request $request)
    {
        \Log::info('Forward request data:', $request->all());
    
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'selected_user_id' => 'required|exists:users,id',
            'is_forwarded' => 'required|boolean',
            'notes' => 'nullable|string'
        ]);
    
        // Find the faculty record
        $faculty = Faculty::findOrFail($request->faculty_id);
        $recipient = User::find($request->selected_user_id);
    
        if (!$recipient) {
            \Log::error('Recipient user not found for ID: ' . $request->selected_user_id);
            return redirect()->back()->with('error', 'Recipient user not found.');
        }
    
        // Create a new lead from faculty data
        $lead = new Lead();
        $lead->name = $faculty->name;
        $lead->email = $faculty->email;
        $lead->phone = $faculty->phone ?? '';
        $lead->location = $faculty->location;
        $lead->courselevel = $faculty->courselevel;
        $lead->lastqualification = $faculty->lastqualifications;
        $lead->passed = $faculty->passed;
        $lead->englishTest = $faculty->englishTest;
        $lead->is_forwarded = true;
        $lead->sources = $recipient->name;
        // Set created_by to the recipient's user ID (IMPORTANT CHANGE)
        $lead->created_by = $recipient->id;
    
        // Set default values for additional required fields
        $lead->locations = $faculty->location;
        $lead->gpa = '';
        $lead->academic = '';
        $lead->higher = '';
        $lead->less = '';
        $lead->score = '';
        $lead->englishscore = '';
        $lead->englishtheory = '';
        $lead->otherScore = '';
        $lead->country = '';
        $lead->university = '';
        $lead->course = '';
        $lead->intake = '';
        $lead->offerss = '';
        $lead->source = '';
        $lead->otherDetails = '';
        $lead->sources = $recipient->name;
        $lead->link = '';
    
        $lead->save();
    
        // Update faculty forwarding status
        $faculty->forwarded_to = $recipient->id;
        $faculty->created_by = $recipient->id;

        $faculty->forwarded_notes = $request->input('notes');
        $faculty->is_forwarded = true;
        $faculty->forwarded_at = now();
        $faculty->save();
    
        return redirect()->route('backend.content.index')
            ->with('success', 'Faculty #' . $faculty->id . ' forwarded to leads successfully!');
    }
    



    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faculty $faculty)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faculty $faculty)
    {
        
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        //
    }
}
