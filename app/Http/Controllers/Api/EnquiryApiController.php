<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\LeadComment;
use App\Models\Name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Gate;

class EnquiryApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view_enquiries')->only('index', 'indexs');
        $this->middleware('can:create_enquiries')->only('create', 'store');
        $this->middleware('can:edit_enquiries')->only('edit', 'update');
        $this->middleware('can:delete_enquiries')->only('destroy');
    }

    public function index()
    {
        $enquiries = Enquiry::all();
        return response()->json($enquiries);
    }

    public function create()
    {
        $names = Name::all();
        return response()->json($names);
    }

    public function store(Request $request)
    {
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

        $enquiries = Enquiry::create($validatedData);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($enquiries)
            ->withProperties([
                'action' => 'created_application',
                'email' => $enquiries->email,
                'phone' => $enquiries->contact,
            ])
            ->log('User created a new application');

        return response()->json(['message' => 'Enquiry saved successfully!', 'enquiry' => $enquiries], 201);
    }

    public function edit($id)
    {
        $enquiries = Enquiry::findOrFail($id);
        return response()->json($enquiries);
    }

    public function update(Request $request, $id)
    {
        $enquiries = Enquiry::findOrFail($id);

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

        $enquiries->update($request->all());

        return response()->json(['message' => 'Enquiry updated successfully', 'enquiry' => $enquiries]);
    }

    public function destroy($id)
    {
        $enquiries = Enquiry::findOrFail($id);
        $enquiries->delete();

        return response()->json(['message' => 'Enquiry deleted successfully']);
    }

    public function import(Request $request)
    {
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
        $reader = IOFactory::createReaderForFile($file->getRealPath());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (count($rows) <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'File is empty or contains only headers'
            ], 422);
        }

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

        DB::beginTransaction();

        try {
            $insertCount = 0;
            $updateCount = 0;
            $errors = [];

            for ($i = 1; $i < count($rows); $i++) {
                $row = array_combine($headers, $rows[$i]);

                if (empty(array_filter($row))) {
                    continue;
                }

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

                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Invalid email format in row " . ($i + 1);
                    continue;
                }

                try {
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
    }

    public function indexs()
    {
        $enquiries = Enquiry::all();
        return response()->json($enquiries);
    }

 public function records($id = null)
    {
        if ($id) {
            $enquirie = Enquiry::findOrFail($id);
            
            // Fetch comments with user relationship
            $lead_comments = LeadComment::with(['user' => function($query) {
                $query->select('id', 'name', 'email', 'avatar'); // Select only needed fields
            }])
            ->where('enquiry_id', $id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($comment) {
                // Transform the comment data to include user information
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'user_id' => $comment->user_id,
                    'enquiry_id' => $comment->enquiry_id,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                    'created_by' => $comment->created_by ?? $comment->user_id,
                    'updated_by' => $comment->updated_by,
                    // User information
                    'author_name' => $comment->user ? $comment->user->name : 'Unknown User',
                    'user_avatar' => $comment->user && $comment->user->avatar ? $comment->user->avatar : '/assets/images/profile/user-6.jpg',
                    'user' => $comment->user ? [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'email' => $comment->user->email,
                        'avatar' => $comment->user->avatar ?? '/assets/images/profile/user-6.jpg'
                    ] : null,
                    'createdBy' => $comment->user ? [
                        'name' => $comment->user->name,
                        'last' => $comment->user->last_name ?? '',
                        'avatar' => $comment->user->avatar ?? '/assets/images/profile/user-6.jpg'
                    ] : null
                ];
            });

            return response()->json([
                'enquirie' => $enquirie,
                'lead_comments' => $lead_comments,
                'enquiry_id' => $id
            ]);
        } else {
            return response()->json([
                'enquirie' => null,
                'lead_comments' => [],
                'enquiry_id' => null
            ]);
        }
    }

    public function storeComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'enquiry_id' => 'required|exists:enquiries,id',
            'comment' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $comment = LeadComment::create([
                'user_id' => $request->user_id,
                'enquiry_id' => $request->enquiry_id,
                'comment' => $request->comment,
                'created_by' => auth()->id()
            ]);

            // Load the user relationship
            $comment->load('user');

            // Return formatted comment data
            $commentData = [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'user_id' => $comment->user_id,
                'enquiry_id' => $comment->enquiry_id,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'created_by' => $comment->created_by,
                'updated_by' => $comment->updated_by,
                'author_name' => $comment->user ? $comment->user->name : 'Unknown User',
                'user_avatar' => $comment->user && $comment->user->avatar ? $comment->user->avatar : '/assets/images/profile/user-6.jpg',
                'user' => $comment->user ? [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'email' => $comment->user->email,
                    'avatar' => $comment->user->avatar ?? '/assets/images/profile/user-6.jpg'
                ] : null,
                'createdBy' => $comment->user ? [
                    'name' => $comment->user->name,
                    'last' => $comment->user->last_name ?? '',
                    'avatar' => $comment->user->avatar ?? '/assets/images/profile/user-6.jpg'
                ] : null
            ];

            return response()->json([
                'success' => true,
                'message' => 'Comment created successfully',
                'data' => $commentData
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating comment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create comment'
            ], 500);
        }
    }

    // Add method to update comment
    public function updateComment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $comment = LeadComment::findOrFail($id);
            
            // Check if user can edit this comment
            if ($comment->user_id !== auth()->id() && $comment->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to edit this comment'
                ], 403);
            }

            $comment->update([
                'comment' => $request->comment,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully',
                'comment' => $request->comment,
                'updated_at' => $comment->updated_at
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating comment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment'
            ], 500);
        }
    }

    // Add method to delete comment
    public function deleteComment($id)
    {
        try {
            $comment = LeadComment::findOrFail($id);
            
            // Check if user can delete this comment
            if ($comment->user_id !== auth()->id() && $comment->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this comment'
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting comment: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment'
            ], 500);
        }

    

    
}
}