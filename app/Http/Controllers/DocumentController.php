<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentForward;
use App\Models\User;
use App\Models\DataEntry;
use Illuminate\Http\Request;
use App\Notifications\DocumentForwarded;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Changed from $documents to $documents for consistency
        $documents = Document::latest()->paginate(5);
        $data_entries = DataEntry::all();
        $users = User::all(); // Get all users for dropdown
        
        return view('backend.document.index', compact('documents', 'users', 'data_entries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data_entries = DataEntry::all();
        return view('backend.document.create', compact('data_entries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'document' => 'required|string|max:255',
            'country' =>  'required|string|max:255',
            'status' =>  'required|string|max:255',

        ]);

        // Store the data in the database
        Document::create($request->all());

        // Redirect back with success message
        return redirect()->route('backend.document.index')->with('success', 'Document saved successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Using singular 'document' for a single record
        $document = Document::findOrFail($id);
        return view('backend.document.update', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the data entry by ID or fail if not found
        // Using singular 'document' for consistency
        $document = Document::findOrFail($id);

        // Validate the request
        $request->validate([
            'document' => 'required|string|max:255',
        ]);

        // Update the data entries
        $document->document = $request->input('document');
        $document->save();

        // Redirect with a success message
        return redirect()->route('backend.document.index')->with('success', 'Document updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Using singular 'document' for consistency
        $document = Document::findOrFail($id);
        $document->delete();

        // Redirect with success message
        return redirect()->route('backend.document.index')->with('success', 'Document deleted successfully');
    }

    /**
     * Forward document to a specific user
     */
    public function forwardDocument(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required|exists:users,id',
            'documentId' => 'required|exists:documents,id',
        ]);

        $user = User::find($request->userId);
        $document = Document::find($request->documentId);

        if (!$document || !$user) {
            return response()->json(['message' => 'User or document not found'], 404);
        }

        // Store the forward record
        DocumentForward::create([
            'user_id' => $user->id,
            'document_id' => $document->id,
            'forwarded_at' => now(),
        ]);
        $lead = Lead::find($id);
    if ($lead) {
        $lead->is_forwarded = true;
        $lead->save();
        // Additional logic for forwarding the document
    }

        // Send notification (combines email and database notification)
        $user->notify(new DocumentForwarded($document));

        return response()->json([
            'success' => true,
            'message' => 'Document forwarded successfully to ' . $user->name
        ]);


    }
    public function showLeadDocuments()
    {
        $country = 'desired_country'; // Replace with dynamic logic if needed

        $documents = Document::where('status', 'lead')
            ->where('country', $country)
            ->paginate(10);

        $data_entries = DataEntry::all();

        return view('backend.document.lead_index', [
            'documents' => $documents,
            'data_entries' => $data_entries
        ]);
    }


    public function refresh()
    {
        return view('backend.script.refresh');
    }
}