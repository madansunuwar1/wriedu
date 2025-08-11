<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class UploadApiController extends Controller
{
    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
        $this->middleware('auth');
        $this->middleware('can:view_uploads')->only(['index', 'show', 'getByApplication', 'getByLead']);
        $this->middleware('can:create_uploads')->only(['create', 'store']);
        $this->middleware('can:edit_uploads')->only(['edit', 'update']);
        $this->middleware('can:delete_uploads')->only(['destroy', 'bulkDestroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the application ID and lead ID from the authenticated user
        $applicationId = auth()->user()->application_id;
        $leadId = auth()->user()->lead_id;
        
        // Fetch uploads that match either the application ID or lead ID
        $uploads = Upload::where(function($query) use ($applicationId, $leadId) {
            $query->where('application_id', $applicationId)
                  ->orWhere('lead_id', $leadId);
        })->orderBy('created_at', 'desc')->get();
        
        // If this is an API request (for Vue component), return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'uploads' => $uploads
            ]);
        }
        
        return view('backend.upload.index', compact('uploads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Retrieve the application ID from the authenticated user or any other source
        $applicationId = auth()->user()->application_id;
        $leadId = auth()->user()->lead_id;
        return view('backend.upload.create', compact('applicationId', 'leadId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Upload request received', [
            'has_files' => $request->hasFile('fileInput'), 
            'application_id' => $request->input('application_id'),
            'lead_id' => $request->input('lead_id')
        ]);

        $request->validate([
            'fileInput.*' => 'required|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif|max:5120',
            'application_id' => 'nullable|exists:applications,id',
            'lead_id' => 'nullable|exists:leads,id',
        ]);

        // Ensure at least one ID is provided
        if (!$request->filled('application_id') && !$request->filled('lead_id')) {
            \Log::error('Missing both application_id and lead_id');
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Either application ID or lead ID must be provided',
                    'errors' => ['id' => ['Either application ID or lead ID must be provided']]
                ], 422);
            }
            
            return back()->withErrors(['id' => 'Either application ID or lead ID must be provided']);
        }

        $uploadedFiles = [];
        $uploadedRecords = [];

        if ($request->hasFile('fileInput')) {
            foreach ($request->file('fileInput') as $file) {
                if ($file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . $originalName;

                    try {
                        $path = $file->storeAs('uploads', $fileName, 'public');
                        \Log::info('Stored file path: ' . $path);

                        if (!$path) {
                            \Log::error('File storage failed: ' . $originalName);
                            
                            if ($request->ajax() || $request->wantsJson()) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'File upload failed during storing. Please try again.',
                                    'errors' => ['fileInput' => ['File upload failed during storing. Please try again.']]
                                ], 500);
                            }
                            
                            return back()->withErrors(['fileInput' => 'File upload failed during storing. Please try again.']);
                        }

                        $uploadData = [
                            'fileInput' => $fileName,
                        ];

                        // Add application_id if it exists
                        if ($request->filled('application_id')) {
                            $uploadData['application_id'] = $request->input('application_id');
                        }

                        // Add lead_id if it exists
                        if ($request->filled('lead_id')) {
                            $uploadData['lead_id'] = $request->input('lead_id');
                        }

                        $upload = Upload::create($uploadData);

                        \Log::info('Upload record created', [
                            'id' => $upload->id, 
                            'filename' => $fileName,
                            'application_id' => $upload->application_id,
                            'lead_id' => $upload->lead_id
                        ]);
                        
                        $uploadedFiles[] = $originalName;
                        $uploadedRecords[] = $upload;
                        
                    } catch (\Exception $e) {
                        \Log::error('Upload exception: ' . $e->getMessage());
                        
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'File upload failed: ' . $e->getMessage(),
                                'errors' => ['fileInput' => ['File upload failed: ' . $e->getMessage()]]
                            ], 500);
                        }
                        
                        return back()->withErrors(['fileInput' => 'File upload failed: ' . $e->getMessage()]);
                    }
                } else {
                    \Log::error('Invalid file upload attempt');
                    
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'File upload failed. The file is not valid.',
                            'errors' => ['fileInput' => ['File upload failed. The file is not valid.']]
                        ], 422);
                    }
                    
                    return back()->withErrors(['fileInput' => 'File upload failed. The file is not valid.']);
                }
            }

            $uploadedFilesList = implode(', ', $uploadedFiles);
            $successMessage = "Files uploaded successfully: {$uploadedFilesList}";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'files' => $uploadedFiles,
                    'uploads' => $uploadedRecords
                ]);
            }

            return redirect()->route('backend.upload.index')->with('success', $successMessage);
        }

        \Log::warning('No files found in upload request');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No files were uploaded',
                'errors' => ['fileInput' => ['No files were uploaded']]
            ], 422);
        }

        return back()->withErrors(['fileInput' => 'File upload failed. No files were selected.']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $upload = Upload::findOrFail($id);
            
            // Check if user has access to this upload
            $applicationId = auth()->user()->application_id;
            $leadId = auth()->user()->lead_id;
            
            if ($upload->application_id !== $applicationId && $upload->lead_id !== $leadId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to upload'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'upload' => $upload
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload not found'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $uploads = Upload::findOrFail($id);
        return view('backend.upload.update', compact('uploads'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Upload $upload)
    {
        $request->validate([
            'application_id' => 'nullable|exists:applications,id',
            'lead_id' => 'nullable|exists:leads,id',
        ]);

        try {
            $updateData = [];
            
            if ($request->filled('application_id')) {
                $updateData['application_id'] = $request->input('application_id');
            }
            
            if ($request->filled('lead_id')) {
                $updateData['lead_id'] = $request->input('lead_id');
            }

            $upload->update($updateData);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Upload updated successfully',
                    'upload' => $upload
                ]);
            }

            return redirect()->route('backend.upload.index')->with('success', 'Upload updated successfully');
            
        } catch (\Exception $e) {
            \Log::error('Update upload exception: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update upload: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to update upload']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $upload = Upload::findOrFail($id);
            
            // Delete the physical file if it exists
            $filePath = 'uploads/' . $upload->fileInput;
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Delete the record from the database
            $upload->delete();

            // Handle both regular and AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Upload deleted successfully'
                ]);
            }

            return redirect()->route('backend.upload.index')->with('success', 'Upload deleted successfully');
            
        } catch (\Exception $e) {
            \Log::error('Delete upload exception: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete upload: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('backend.upload.index')->with('error', 'Failed to delete upload');
        }
    }

    /**
     * Get uploads by application ID
     */
    public function getByApplication($applicationId)
    {
        try {
            $uploads = Upload::where('application_id', $applicationId)
                           ->orderBy('created_at', 'desc')
                           ->get();

            return response()->json([
                'success' => true,
                'uploads' => $uploads
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch uploads'
            ], 500);
        }
    }

    /**
     * Get uploads by lead ID
     */
    public function getByLead($leadId)
    {
        try {
            $uploads = Upload::where('lead_id', $leadId)
                           ->orderBy('created_at', 'desc')
                           ->get();

            return response()->json([
                'success' => true,
                'uploads' => $uploads
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch uploads'
            ], 500);
        }
    }

    /**
     * Bulk delete uploads
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:uploads,id'
        ]);

        try {
            $uploads = Upload::whereIn('id', $request->ids)->get();
            $deletedCount = 0;

            foreach ($uploads as $upload) {
                // Delete the physical file if it exists
                $filePath = 'uploads/' . $upload->fileInput;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }

                // Delete the record from the database
                $upload->delete();
                $deletedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} uploads"
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Bulk delete uploads exception: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete uploads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download/view uploaded file
     */
    public function download($filename)
    {
        try {
            $filePath = 'uploads/' . $filename;
            
            if (!Storage::disk('public')->exists($filePath)) {
                abort(404, 'File not found');
            }

            // Get the file
            $file = Storage::disk('public')->get($filePath);
            $mimeType = Storage::disk('public')->mimeType($filePath);

            return Response::make($file, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Download file exception: ' . $e->getMessage());
            abort(404, 'File not found');
        }
    }

    /**
     * Get human-readable upload error message
     */
    private function getUploadErrorMessage($errorCode)
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        return isset($errors[$errorCode]) ? $errors[$errorCode] : 'Unknown upload error';
    }
}