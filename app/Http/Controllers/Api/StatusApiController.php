<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Document;
use App\Models\DataEntry;
use Illuminate\Http\Request;

class StatusApiController extends Controller
{
    public function index()
    {
        $documents = Document::latest()->get();
        $data_entries = DataEntry::select('country')->distinct()->get();
        
        return response()->json([
            'documents' => $documents,
            'data_entries' => $data_entries,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'document' => 'required|string|max:255',
            'country' =>  'required|string|max:255',
            'status' =>  'required|string|in:lead,application',
        ]);

        $document = Document::create($validatedData);

        return response()->json([
            'document' => $document,
            'message' => 'Document saved successfully!'
        ], 201);
    }
    
    public function destroy(Document $document)
    {
        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully'
        ]);
    }
    public function updateIds(Request $request)
{
    try {
        // Validate the request
        $request->validate([
            'documents' => 'required|array',
            'documents.*.currentId' => 'required|integer',
            'documents.*.newId' => 'required|integer',
            'documents.*.document' => 'required|string',
            'documents.*.country' => 'required|string',
            'documents.*.status' => 'required|string'
        ]);
        
        $documents = $request->input('documents');
        
        DB::transaction(function () use ($documents) {
            // Step 1: Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            try {
                // Step 2: Create temporary table with exact structure
                DB::statement('CREATE TEMPORARY TABLE temp_documents LIKE documents');
                
                // Step 3: Insert all records into temp table with new IDs
                foreach ($documents as $doc) {
                    DB::table('temp_documents')->insert([
                        'id' => $doc['newId'],
                        'document' => $doc['document'],
                        'country' => $doc['country'],
                        'status' => $doc['status'],
                        'created_at' => isset($doc['created_at']) ? $doc['created_at'] : now(),
                        'updated_at' => now()
                    ]);
                }
                
                // Step 4: Clear original table and insert from temp
                DB::table('documents')->truncate();
                DB::statement('INSERT INTO documents SELECT * FROM temp_documents');
                
                // Step 5: Drop temp table
                DB::statement('DROP TEMPORARY TABLE temp_documents');
                
                // Step 6: Reset auto increment to match highest ID
                $maxId = collect($documents)->max('newId');
                DB::statement("ALTER TABLE documents AUTO_INCREMENT = " . ($maxId + 1));
                
            } finally {
                // Step 7: Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        });
        
        return response()->json([
            'message' => 'Document order updated successfully',
            'success' => true
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors(),
            'success' => false
        ], 422);
        
    } catch (\Exception $e) {
        \Log::error('Error updating document IDs: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'message' => 'Failed to update document order: ' . $e->getMessage(),
            'error' => $e->getMessage(),
            'success' => false
        ], 500);
    }
}
}