<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Models\DataEntry;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\db;



class UniversityApiController extends Controller
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
    public function view()
    {
        $universities = University::all();
        return response()->json($universities);
    }

    public function stores(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image_link' => 'required|url',
            'background_image' => 'nullable|url',
        ]);

        $university = University::create($request->all());

        return response()->json($university, 201);
    }

    public function updates(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image_link' => 'required|url',
            'background_image' => 'nullable|url',
        ]);

        $university = University::findOrFail($id);
        $university->update($request->all());

        return response()->json($university, 200);
    }

    public function delete($id)
    {
        $university = University::findOrFail($id);
        $university->delete();

        return response()->json(null, 204);
    }


      public function getManagedUniversities()
    {
        $universities = University::orderBy('name')->get();
        return response()->json($universities);
    }

    /**
     * Store a new university in the 'universities' table.
     */
    public function storeManagedUniversity(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:universities,name',
            'image_link' => 'required|url',
            'background_image' => 'nullable|url',
        ]);

        $university = University::create($request->all());
        return response()->json($university, 201);
    }

    /**
     * Update an existing university in the 'universities' table.
     */
    public function updateManagedUniversity(Request $request, $id)
    {
        $university = University::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:universities,name,' . $university->id,
            'image_link' => 'required|url',
            'background_image' => 'nullable|url',
        ]);
        
        $university->update($request->all());
        return response()->json($university, 200);
    }

    /**
     * Delete a university from the 'universities' table.
     */
    public function destroyManagedUniversity($id)
    {
        $university = University::findOrFail($id);
        $university->delete();
        return response()->json(null, 204);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data_entries = DataEntry::all();
    return response()->json($data_entries);
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
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.dataentry.create');
    }
    
    public function show($id)
    {
        // Use findOrFail to automatically handle the case where the ID doesn't exist.
        // It will return a 404 Not Found response, which is the correct RESTful behavior.
        try {
            $data_entry = DataEntry::findOrFail($id);
            return response()->json($data_entry);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Record not found'], 404);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
       public function store(Request $request): JsonResponse // <-- Return type is JsonResponse
    {
        // Validate the incoming data.
        // 'nullable' is used for optional fields, which is more robust than requiring 'N/A'.
        $validatedData = $request->validate([
            'newUniversity' => 'required|string|max:255',
            'newLocation' => 'required|string|max:255',
            'newCourse' => 'required|string|max:255',
            'newIntake' => 'required|string',
            'country' => 'required|string',
            'requireddocuments' => 'required|string',
            'level' => 'required|string',

            // Optional fields are marked as 'nullable'
            'newScholarship' => 'nullable|string',
            'newAmount' => 'nullable|string',
            'newIelts' => 'nullable|string',
            'newpte' => 'nullable|string',
            'newug' => 'nullable|string',
            'newgpaug' => 'nullable|string',
            'newPgIelts' => 'nullable|string',
            'newPgPte' => 'nullable|string',
            'newpg' => 'nullable|string',
            'newgpapg' => 'nullable|string',
            'newtest' => 'nullable|string',
        ]);

        try {
            // Use a database transaction for safety
            DB::beginTransaction();
            
            // Create the DataEntry using the validated data
            $dataEntry = DataEntry::create($validatedData);

            DB::commit();

            // Return a JSON success response with a 201 "Created" status
            return response()->json([
                'message' => 'University data created successfully!',
                'data' => $dataEntry
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating data entry: ' . $e->getMessage());
            
            // Return a generic error message
            return response()->json([
                'message' => 'An unexpected error occurred while saving the data.'
            ], 500);
        }
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

            // Column mappings - only university and location are required
            $columnMappings = [
                'university' => ['dbField' => 'newUniversity', 'required' => true],
                'location' => ['dbField' => 'newLocation', 'required' => true],
                'course' => ['dbField' => 'newCourse', 'required' => false],
                'intake' => ['dbField' => 'newIntake', 'required' => false],
                'scholarship' => ['dbField' => 'newScholarship', 'required' => false],
                'tuition' => ['dbField' => 'newAmount', 'required' => false],
                'ug ielts' => ['dbField' => 'newIelts', 'required' => false],
                'ug pte' => ['dbField' => 'newpte', 'required' => false],
                'pg ielts' => ['dbField' => 'newPgIelts', 'required' => false],
                'pg pte' => ['dbField' => 'newPgPte', 'required' => false],
                'ug gap' => ['dbField' => 'newug', 'required' => false],
                'pg gap' => ['dbField' => 'newpg', 'required' => false],
                'ug gpa or percentage' => ['dbField' => 'newgpaug', 'required' => false],
                'pg gpa or percentage' => ['dbField' => 'newgpapg', 'required' => false],
                'english test' => ['dbField' => 'newtest', 'required' => false],
                'country' => ['dbField' => 'country', 'required' => false],
                'required documents' => ['dbField' => 'requireddocuments', 'required' => false],
                'level' => ['dbField' => 'level', 'required' => false],
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

                if (!empty($rowData)) {
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
                $updateCount = 0;
                $insertCount = 0;

                foreach ($data as $entry) {
                    // Check for existing record
                    $existingRecord = DataEntry::where([
                        'newUniversity' => $entry['newUniversity'],
                        'newCourse' => $entry['newCourse'] ?? null,
                        'newLocation' => $entry['newLocation']
                    ])->first();

                    if ($existingRecord) {
                        $existingRecord->update($entry);
                        $updateCount++;
                    } else {
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
        return view('backend.dataentrys.indexs', compact('data_entries', 'universities'));
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
            $documents = array_filter($documents, function ($item) {
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
    public function universityprofile($id)
{
    // This method's only job now is to load the Vue host view
    // and pass the university ID to it.
    return view('backend.notice.university_profile_vue', ['id' => $id]);
}

public function getCourseFinderData(): JsonResponse
{
    try {
        $data_entries = DataEntry::orderBy('newUniversity')->get();
        
        // This is the most efficient way for JS: key the arrays by the university name.
        $universities = University::all()->keyBy('name'); 
        $images = University::all()->keyBy('image_link');

        return response()->json([
            'data_entries' => $data_entries,
            'universities' => $universities,
            'images' => $images,
        ]);

    } catch (\Exception $e) {
        Log::error('Course Finder API Error: ' . $e->getMessage());
        return response()->json(['message' => 'Could not retrieve course finder data.'], 500);
    }
}

public function getUniversityProfileData($id): JsonResponse
{
    try {
        // Fetch the specific university data entry
        $universityData = DataEntry::findOrFail($id);

        // Find the matching university profile (for image, etc.)
        $matchedUniversity = University::where('name', $universityData->newUniversity)->first();

        // Return both data objects in a structured JSON response
        return response()->json([
            'university' => $universityData,
            'matchedUniversity' => $matchedUniversity,
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['message' => 'University not found.'], 404);
    } catch (\Exception $e) {
        // Generic error handler
        return response()->json(['message' => 'An error occurred while fetching data.'], 500);
    }
}
}


