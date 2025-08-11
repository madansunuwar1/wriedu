<?php

namespace App\Http\Controllers;

use App\Models\DataEntry;
use App\Models\University;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use Illuminate\Support\Facades\Gate;

class DataEntryController extends Controller
{

    public function __construct(NotificationController $notificationController)
{
    $this->notificationController = $notificationController;
    $this->middleware('auth');
    $this->middleware('can:view_data_entries')->only('index', 'indexs');
    $this->middleware('can:create_data_entries')->only('create', 'store');
    $this->middleware('can:edit_data_entries')->only('edit', 'update');
    $this->middleware('can:delete_data_entries')->only('destroy');

}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data_entries = DataEntry::all();
        return view('backend.dataentry.index', compact('data_entries'));
    }

   

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.dataentry.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'newUniversity' => 'required|string|max:255',
            'newLocation' => 'required|string|max:255',
            'newCourse' => 'required|string|max:255',
            'newIntake' => 'required|string|max:255',
            'newScholarship' => 'required|string|max:255',
            'newAmount' => 'required|string|max:255',
            'newIelts' => 'required|string|max:255',
            'newpte' => 'required|string|max:255',
            'newPgIelts' => 'required|string|max:255',
            'newPgPte' => 'required|string|max:255',
            'newug' => 'required|string|max:255',
            'newpg' => 'required|string|max:255',
            'newgpaug' => 'required|string|max:255',
            'newgpapg' => 'required|string|max:255',
            'newtest' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'requireddocuments' => 'required|string|max:255',
            'level' => 'required|string|max:255',
        ]);

        // Store the data in the database
        DataEntry::create($request->all());

        // Redirect back with success message
        return redirect()->route('backend.dataentrys.indexs')->with('success', 'Data saved successfully!');
    }


    
   

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data_entries = DataEntry::findOrFail($id);
        return view('backend.dataentry.update', compact('data_entries'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    // Find the data entry by ID or fail if not found
    $data_entries = DataEntry::findOrFail($id);

    // Validate the request
    $request->validate([
        'newUniversity' => 'required|string|max:255',
            'newLocation' => 'required|string|max:255',
            'newCourse' => 'required|string|max:255',
            'newIntake' => 'required|string|max:255',
            'newScholarship' => 'required|string|max:255',
            'newAmount' => 'required|string|max:255',
            'newIelts' => 'required|string|max:255',
            'newpte' => 'required|string|max:255',
            'newPgIelts' => 'required|string|max:255',
            'newPgPte' => 'required|string|max:255',
            'newug' => 'required|string|max:255',
            'newpg' => 'required|string|max:255',
            'newgpaug' => 'required|string|max:255',
            'newgpapg' => 'required|string|max:255',
            'newtest' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'requireddocuments' => 'required|string|max:255',
            'level' => 'required|string|max:255',
    ]);

    // Update the data entries
    $data_entries->newUniversity = $request->input('newUniversity');
    $data_entries->newLocation = $request->input('newLocation');
    $data_entries->newCourse = $request->input('newCourse');
    $data_entries->newIntake = $request->input('newIntake');
    $data_entries->newScholarship = $request->input('newScholarship');
    $data_entries->newAmount = $request->input('newAmount');
    $data_entries->newIelts = $request->input('newIelts');
    $data_entries->newpte = $request->input('newpte');
    $data_entries->newPgIelts = $request->input('newPgIelts');
    $data_entries->newPgPte = $request->input('newPgPte');
    $data_entries->newug = $request->input('newug');
    $data_entries->newpg = $request->input('newpg');
    $data_entries->newgpaug = $request->input('newgpaug');
    $data_entries->newgpapg = $request->input('newgpapg');
    $data_entries->newtest = $request->input('newtest');
    $data_entries->country = $request->input('country');
    $data_entries->requireddocuments = $request->input('requireddocuments');
    $data_entries->level = $request->input('level');
    // Save the updated data entry
    $data_entries->save();

    // Redirect with a success message
    return redirect()->route('backend.dataentry.index')->with('success', 'Data entry updated successfully');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data_entries = DataEntry::findOrFail($id);

        

        // Delete the comment from the database
        $data_entries->delete();

        // Redirect with success message
        return redirect()->route('backend.dataentry.index')->with('success', 'data entry deleted successfully');
    }
    public function import(Request $request)
    {
        try {
            // Validate file presence and type
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx,csv|max:10240', // 10MB max
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please upload a valid Excel or CSV file (max 10MB)'
                ], 422);
            }
    
            $file = $request->file('file');
            
            // Load the spreadsheet
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, true, true);
    
            if (count($rows) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'File is empty or contains only headers'
                ], 422);
            }
    
            // Column mappings with validation rules
            $columnMappings = [
                'university' => ['dbField' => 'newUniversity', 'required' => true],
                'location' => ['dbField' => 'newLocation', 'required' => true],
                'course' => ['dbField' => 'newCourse', 'required' => true],
                'intake' => ['dbField' => 'newIntake', 'required' => true],
                'scholarship' => ['dbField' => 'newScholarship', 'required' => true],
                'tuition' => ['dbField' => 'newAmount', 'required' => true],
                'ug ielts' => ['dbField' => 'newIelts', 'required' => true],
                'ug pte' => ['dbField' => 'newpte', 'required' => true],
                'pg ielts' => ['dbField' => 'newPgIelts', 'required' => true],
                'pg pte' => ['dbField' => 'newPgPte', 'required' => true],
                'ug gap' => ['dbField' => 'newug', 'required' => true],
                'pg gap' => ['dbField' => 'newpg', 'required' => true],
                'ug gpa or percentage' => ['dbField' => 'newgpaug', 'required' => true],
                'pg gpa or percentage' => ['dbField' => 'newgpapg', 'required' => true],
                'english test' => ['dbField' => 'newtest', 'required' => true],
                'country' => ['dbField' => 'country', 'required' => true],
                'required documents' => ['dbField' => 'requireddocuments', 'required' => true],
                'level' => ['dbField' => 'level', 'required' => true],

            ];
    
            // Validate headers
            $headers = array_map('trim', array_map('strtolower', $rows[1]));
            $requiredHeaders = array_keys($columnMappings);
            $missingHeaders = array_diff($requiredHeaders, array_map('trim', array_map('strtolower', $headers)));
    
            if (!empty($missingHeaders)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required columns: ' . implode(', ', $missingHeaders)
                ], 422);
            }
    
            // Process data
            $data = [];
            $errors = [];
            $rowNumber = 2;
            $updateCount = 0;
            $insertCount = 0;
    
            foreach (array_slice($rows, 1) as $row) {
                $rowData = [];
                $hasData = false;
    
                // Check if row has any data
                foreach ($row as $value) {
                    if (!empty(trim((string)$value))) {
                        $hasData = true;
                        break;
                    }
                }
    
                if (!$hasData) continue;
    
                foreach ($headers as $colIndex => $header) {
                    $headerKey = array_search(strtolower(trim($header)), array_map('strtolower', $requiredHeaders));
                    if ($headerKey !== false) {
                        $mapping = $columnMappings[$requiredHeaders[$headerKey]];
                        $value = trim((string)($row[$colIndex] ?? ''));
                        
                        if ($mapping['required'] && empty($value)) {
                            $errors[] = "Row {$rowNumber}: {$header} is required";
                            continue 2; // Skip this row
                        }
    
                        $rowData[$mapping['dbField']] = $value;
                    }
                }
    
                if (count($rowData) === count($columnMappings)) {
                    $data[] = $rowData;
                }
                $rowNumber++;
            }
    
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid data found in the file'
                ], 422);
            }
    
            // Begin transaction
            \DB::beginTransaction();
            try {
                foreach ($data as $entry) {
                    // Check for existing record
                    $existingRecord = DataEntry::where([
                        'newUniversity' => $entry['newUniversity'],
                        'newCourse' => $entry['newCourse'],
                        'newLocation' => $entry['newLocation']
                    ])->first();
    
                    if ($existingRecord) {
                        // Update existing record
                        $existingRecord->update($entry);
                        $updateCount++;
                    } else {
                        // Create new record
                        DataEntry::create($entry);
                        $insertCount++;
                    }
                }
                \DB::commit();
    
                $message = "{$insertCount} records inserted, {$updateCount} records updated successfully";
                if (!empty($errors)) {
                    $message .= ". Warnings: " . implode('; ', $errors);
                }
    
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
    
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }
    
        } catch (SpreadsheetException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading spreadsheet: ' . $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while importing the file. Please check the file format and try again.'
            ], 500);
        }
    }






















    public function indexs()
    {
        $data_entries = DataEntry::all();
        $universities = University::all();
        return view('backend.dataentrys.indexs', compact('data_entries','universities'));
    }



    public function getCountries()
    {
        try {
            $countries = DataEntry::select('country')
                ->distinct()
                ->whereNotNull('country')
                ->where('country', '!=', '')
                ->orderBy('country')
                ->pluck('country');
            
            return response()->json([
                'status' => 'success',
                'data' => $countries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch countries'
            ], 500);
        }
    }

    public function getLocationsByCountry(Request $request)
    {
        try {
            $country = $request->query('country');
            
            if (!$country) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Country parameter is required'
                ], 400);
            }

            $locations = DataEntry::select('newLocation')
                ->where('country', $country)
                ->whereNotNull('newLocation')
                ->where('newLocation', '!=', '')
                ->distinct()
                ->orderBy('newLocation')
                ->pluck('newLocation');

            return response()->json([
                'status' => 'success',
                'data' => $locations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch locations'
            ], 500);
        }
    }

    public function getUniversitiesByLocation(Request $request)
    {
        try {
            $location = $request->query('location');
            
            if (!$location) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Location parameter is required'
                ], 400);
            }

            $universities = DataEntry::select('newUniversity')
                ->where('newLocation', $location)
                ->whereNotNull('newUniversity')
                ->where('newUniversity', '!=', '')
                ->distinct()
                ->orderBy('newUniversity')
                ->pluck('newUniversity');

            return response()->json([
                'status' => 'success',
                'data' => $universities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch universities'
            ], 500);
        }
    }

    public function getCoursesByUniversity(Request $request)
    {
        try {
            $university = $request->query('university');
            $location = $request->query('location');
            
            
            if (!$university || !$location) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'University and location parameters are required'
                ], 400);
            }

            $courses = DataEntry::select('newCourse')
                ->where('newUniversity', $university)
                ->where('newLocation', $location)
                ->whereNotNull('newCourse')
                ->where('newCourse', '!=', '')
                ->distinct()
                ->orderBy('newCourse')
                ->pluck('newCourse');

            return response()->json([
                'status' => 'success',
                'data' => $courses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch courses'
            ], 500);
        }
    }

    public function getIntakesByCourse(Request $request)
    {
        try {
            $course = $request->query('course');
            $university = $request->query('university');
            $location = $request->query('location');
            
            if (!$course || !$university || !$location) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Course, university, and location parameters are required'
                ], 400);
            }

            $intakes = DataEntry::select('newIntake')
                ->where('newCourse', $course)
                ->where('newUniversity', $university)
                ->where('newLocation', $location)
                ->whereNotNull('newIntake')
                ->where('newIntake', '!=', '')
                ->distinct()
                ->orderBy('newIntake')
                ->pluck('newIntake');

            return response()->json([
                'status' => 'success',
                'data' => $intakes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch intakes'
            ], 500);
        }
    }


    public function getRequiredDocuments(Request $request)
{
    try {
        $university = $request->query('university');
        $country = $request->query('country');
        
        if (!$university || !$country) {
            return response()->json([
                'status' => 'error',
                'message' => 'University and country parameters are required'
            ], 400);
        }

        // Query the database for required documents
        $entry = DataEntry::where('newUniversity', $university)
                    ->where('country', $country)
                    ->first();
        
        if (!$entry || empty($entry->requireddocuments)) {
            // Return a default set of documents if none are specified
            return response()->json([
                'status' => 'success',
                'data' => [
                    'Passport',
                    'Academic Transcripts',
                    'Statement of Purpose',
                    'CV/Resume',
                    'Reference Letters',
                    'English Proficiency Test Results'
                ]
            ]);
        }
        
        // If the documents are stored as a comma-separated string
        $documents = explode(',', $entry->requireddocuments);
        
        // If documents are stored as JSON
        // $documents = json_decode($entry->requireddocuments, true);
        
        // Clean up the array
        $documents = array_map('trim', $documents);
        $documents = array_filter($documents, function($item) {
            return !empty($item);
        });
        
        return response()->json([
            'status' => 'success',
            'data' => $documents
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch required documents: ' . $e->getMessage()
        ], 500);
    }
}




  


}
