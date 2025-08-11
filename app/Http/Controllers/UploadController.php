<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class UploadController extends Controller
{

    public function __construct(NotificationController $notificationController)
{
    $this->notificationController = $notificationController;
    $this->middleware('auth');
    $this->middleware('can:view_uploads')->only('index');
    $this->middleware('can:create_uploads')->only('create', 'store');
    $this->middleware('can:edit_uploads')->only('edit', 'update');
    $this->middleware('can:delete_uploads')->only('destroy');

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
        })->get();
        
        return view('backend.upload.index', compact('uploads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    // Retrieve the application ID from the authenticated user or any other source
    $applicationId = auth()->user()->application_id; // Example: retrieve from authenticated user
    $leadId = auth()->user()->lead_id;
    return view('backend.upload.create', compact('applicationId'));
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
        return back()->withErrors(['id' => 'Either application ID or lead ID must be provided']);
    }

    $uploadedFiles = [];

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
                } catch (\Exception $e) {
                    \Log::error('Upload exception: ' . $e->getMessage());
                    return back()->withErrors(['fileInput' => 'File upload failed: ' . $e->getMessage()]);
                }
            } else {
                \Log::error('Invalid file upload attempt');
                return back()->withErrors(['fileInput' => 'File upload failed. The file is not valid.']);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Files uploaded successfully: ' . implode(', ', $uploadedFiles),
                'files' => $uploadedFiles
            ]);
        }

        $uploadedFilesList = implode(', ', $uploadedFiles);
        return redirect()->route('backend.upload.index')->with('success', "Files uploaded successfully: {$uploadedFilesList}");
    }

    \Log::warning('No files found in upload request');

    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'success' => false,
            'message' => 'No files were uploaded'
        ], 400);
    }

    return back()->withErrors(['fileInput' => 'File upload failed. No files were selected.']);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $uploads = Upload::findOrFail($id);

        

        // Delete the comment from the database
        $uploads->delete();

        // Redirect with success message
        return redirect()->route('backend.upload.index')->with('success', 'upload deleted successfully');
    }
}
