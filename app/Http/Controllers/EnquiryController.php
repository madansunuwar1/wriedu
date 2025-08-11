<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\LeadComment;
use App\Models\Name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Gate;

class EnquiryController extends Controller
{

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
        $this->middleware('auth');
        $this->middleware('can:view_enquiries')->only('index', 'indexs');
        $this->middleware('can:create_enquiries')->only('create', 'store');
        $this->middleware('can:edit_enquiries')->only('edit', 'update');
        $this->middleware('can:delete_enquiries')->only('destroy');
    
    
    }
   


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enquiries = Enquiry::all();
        return view('backend.enquiry.index', compact('enquiries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $names = Name::all();   
        return view('backend.enquiry.create', compact('names'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'uname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact' => 'required|string|max:255',
            'guardians' => 'required|string|max:255',
            'contacts' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'education' => 'required|string|max:255',
            'about' => 'required|string|max:255',
            'ielts' => 'required|string|max:255',
            'toefl' => 'required|string|max:255',
            'ellt' => 'required|string|max:255',
            'pte' => 'required|string|max:255',
            'sat' => 'required|string|max:255',
            'gre' => 'required|string|max:255',
            'gmat' => 'required|string|max:255',
            'other' => 'required|string|max:255',
            'feedback' => 'required|string|max:255',
            'counselor' => 'required|string|max:255',
            'institution1' => 'required|string|max:255',
            'grade1' => 'required|string|max:255',
            'year1' => 'required|string|max:255',
            'institution2' => 'required|string|max:255',
            'grade2' => 'required|string|max:255',
            'year2' => 'required|string|max:255',
            'institution3' => 'required|string|max:255',
            'grade3' => 'required|string|max:255',
            'year3' => 'required|string|max:255',
            'institution4' => 'required|string|max:255',
            'grade4' => 'required|string|max:255',
            'year4' => 'required|string|max:255',
        ]);
    
        // Store the validated data in the database
       $enquiries = Enquiry::create($validatedData);

        activity()
    ->causedBy(auth()->user())
    ->performedOn($enquiries)
    ->withProperties([
        'action' => 'created_application',
        'email' => $enquiries->email,
        'phone' => $enquiries->phone,
    ])
    ->log('User created a new application');
    
        // Redirect back with a success message
        return redirect()->route('backend.enquiry.index', compact('enquiries'))->with('success', 'Enquiry saved successfully!');
    }



    public function edit($id)
    {
        $enquiries = Enquiry::findOrFail($id);
        return view('backend.enquiry.update', compact('enquiries'));

    }

    public function update(Request $request, $id)
    {
        // Find the data entry by ID or fail if not found
        $enquiries = Enquiry::findOrFail($id);
    
        // Validate the request
        $request->validate([
            'uname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact' => 'required|string|max:255',
            'guardians' => 'required|string|max:255',
            'contacts' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'education' => 'required|string|max:255',
            'about' => 'required|string|max:255',
            'ielts' => 'required|string|max:255',
            'toefl' => 'required|string|max:255',
            'ellt' => 'required|string|max:255',
            'pte' => 'required|string|max:255',
            'sat' => 'required|string|max:255',
            'gre' => 'required|string|max:255',
            'gmat' => 'required|string|max:255',
            'other' => 'required|string|max:255',
            'feedback' => 'required|string|max:255',
            'counselor' => 'required|string|max:255',
            'institution1' => 'required|string|max:255',
            'grade1' => 'required|string|max:255',
            'year1' => 'required|string|max:255',
            'institution2' => 'required|string|max:255',
            'grade2' => 'required|string|max:255',
            'year2' => 'required|string|max:255',
            'institution3' => 'required|string|max:255',
            'grade3' => 'required|string|max:255',
            'year3' => 'required|string|max:255',
            'institution4' => 'required|string|max:255',
            'grade4' => 'required|string|max:255',
            'year4' => 'required|string|max:255',
        ]);
    
        // Update the data entries
        $enquiries->uname = $request->input('uname');
        $enquiries->email = $request->input('email');
        $enquiries->contact = $request->input('contact');
        $enquiries->guardians = $request->input('guardians');
        $enquiries->contacts = $request->input('contacts');
        $enquiries->country = $request->input('country');
        $enquiries->education = $request->input('education');
        $enquiries->about = $request->input('about');
        $enquiries->ielts = $request->input('ielts');
        $enquiries->toefl = $request->input('toefl');
        $enquiries->ellt = $request->input('ellt');
        $enquiries->pte = $request->input('pte');
        $enquiries->sat = $request->input('sat');
        $enquiries->gre = $request->input('gre');
        $enquiries->gmat = $request->input('gmat');
        $enquiries->other = $request->input('other');
        $enquiries->feedback = $request->input('feedback');
        $enquiries->counselor = $request->input('counselor');
        $enquiries->institution1 = $request->input('institution1');
        $enquiries->grade1 = $request->input('grade1');
        $enquiries->year1 = $request->input('year1');
        $enquiries->institution2 = $request->input('institution2');
        $enquiries->grade2 = $request->input('grade2');

        $enquiries->year2 = $request->input('year2');
        $enquiries->institution3 = $request->input('institution3');
        $enquiries->grade3 = $request->input('grade3');
        $enquiries->year3 = $request->input('year3');
        $enquiries->institution4 = $request->input('institution4');
        $enquiries->grade4 = $request->input('grade4');
        $enquiries->year4 = $request->input('year4');
    
        // Save the updated data entry
        $enquiries->save();
    
        // Redirect with a success message
        return redirect()->route('backend.enquiry.index')->with('success', 'Enquiry updated successfully');
    }

    public function destroy($id)
    {
        $enquiries = Enquiry::findOrFail($id);

        

        // Delete the comment from the database
        $enquiries->delete();

        // Redirect with success message
        return redirect()->route('backend.enquiry.index')->with('success', 'Enquiry deleted successfully');
    }



    public function import(Request $request)
    {
        try {
            // Validate the uploaded file
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $file = $request->file('file');
            
            // Load the spreadsheet
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Validate if file has data
            if (count($rows) <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'File is empty or contains only headers'
                ], 422);
            }

            // Get and validate headers
            $headers = array_map('strtolower', array_map('trim', $rows[0]));
            $requiredHeaders = [
                'student name',
                'email id',
                'contact number',
                'guardian name',
                'guardian number',
                'country',
                'education'
            ];

            $missingHeaders = array_diff($requiredHeaders, $headers);
            if (!empty($missingHeaders)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required columns: ' . implode(', ', $missingHeaders)
                ], 422);
            }

            // Begin transaction
            DB::beginTransaction();
            
            try {
                $insertCount = 0;
                $updateCount = 0;
                $errors = [];
                
                // Process data rows
                for ($i = 1; $i < count($rows); $i++) {
                    $row = array_combine($headers, $rows[$i]);
                    
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Map data to model fields
                    $data = [
                        'uname' => trim($row['student name']),
                        'email' => trim($row['email id']),
                        'contact' => trim($row['contact number']),
                        'guardians' => trim($row['guardian name']),
                        'contacts' => trim($row['guardian number']),
                        'country' => trim($row['country']),
                        'education' => trim($row['education']),
                        'about' => $row['know about us'] ?? null,
                        'ielts' => $row['ielts'] ?? null,
                        'toefl' => $row['toefl'] ?? null,
                        'ellt' => $row['ellt'] ?? null,
                        'pte' => $row['pte'] ?? null,
                        'sat' => $row['sat'] ?? null,
                        'gre' => $row['gre'] ?? null,
                        'gmat' => $row['gmat'] ?? null,
                        'other' => $row['other test score'] ?? null,
                        'feedback' => $row['feedback'] ?? null,
                        'counselor' => $row['counselor'] ?? null,
                        'institution1' => $row['institution 1'] ?? null,
                        'grade1' => $row['grade 1'] ?? null,
                        'year1' => $row['year 1'] ?? null,
                        'institution2' => $row['institution 2'] ?? null,
                        'grade2' => $row['grade 2'] ?? null,
                        'year2' => $row['year 2'] ?? null,
                        'institution3' => $row['institution 3'] ?? null,
                        'grade3' => $row['grade 3'] ?? null,
                        'year3' => $row['year 3'] ?? null,
                        'institution4' => $row['institution 4'] ?? null,
                        'grade4' => $row['grade 4'] ?? null,
                        'year4' => $row['year 4'] ?? null
                    ];

                    // Validate email
                    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Invalid email format in row " . ($i + 1);
                        continue;
                    }

                    try {
                        // Check if record exists
                        $enquiry = Enquiry::where('email', $data['email'])->first();
                        
                        if ($enquiry) {
                            $enquiry->update($data);
                            $updateCount++;
                        } else {
                            Enquiry::create($data);
                            $insertCount++;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error processing row " . ($i + 1) . ": " . $e->getMessage();
                    }
                }

                if ($insertCount === 0 && $updateCount === 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No valid data was processed'
                    ], 422);
                }

                DB::commit();

                $message = sprintf(
                    '%d records inserted, %d records updated successfully',
                    $insertCount,
                    $updateCount
                );

                if (!empty($errors)) {
                    $message .= "\nErrors encountered: " . implode('; ', $errors);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'inserted' => $insertCount,
                        'updated' => $updateCount,
                        'errors' => $errors
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while importing the data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function indexs()
    {
        $enquiries = Enquiry::all();
        return view('backend.enquiryhistory.indexs', compact('enquiries'));
    }

    public function records($id = null)
{
    // If an ID is provided, fetch the specific enquiry by ID and ensure it exists
    if ($id) {
        $enquirie = Enquiry::findOrFail($id);  // Retrieve enquiry by ID
        
        // Use enquiry_id to match the actual column name from your migration
        $lead_comments = LeadComment::where('enquiry_id', $id)->get();
        
        // Return the view with the necessary data
        return view('backend.enquiryhistory.records', [
            'enquirie' => $enquirie,
            'lead_comments' => $lead_comments,
            'enquiry_id' => $id  // Pass the ID to the view
        ]);
    } else {
        // No specific enquiry, set to null
        return view('backend.enquiryhistory.records', [
            'enquirie' => null,
            'lead_comments' => collect(), // Empty collection if no enquiry ID
            'enquiry_id' => null
        ]);
    }
}
    



    
}
