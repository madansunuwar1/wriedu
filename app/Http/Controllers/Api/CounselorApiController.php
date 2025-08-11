<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // Import Validator

class CounselorApiController extends Controller
{
    /**
     * Return all counselors as JSON.
     */
    public function index()
    {
        $names = Name::orderBy('created_at', 'desc')->get(); // Get newest first
        return response()->json($names);
    }

    /**
     * Store a new counselor.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:names,name', // Added unique validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Return validation errors
        }

        $name = Name::create($request->all());

        return response()->json([
            'message' => 'Counselor added successfully!',
            'data' => $name
        ], 201); // 201 Created status
    }

    /**
     * Update an existing counselor.
     */
    public function update(Request $request, $id)
    {
        $name = Name::findOrFail($id);

        $validator = Validator::make($request->all(), [
            // Ensure the name is unique, but ignore the current counselor's name
            'name' => 'required|string|max:255|unique:names,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $name->update($request->all());

        return response()->json([
            'message' => 'Counselor updated successfully!',
            'data' => $name
        ]);
    }

    /**
     * Delete a counselor.
     */
    public function destroy($id)
    {
        $name = Name::findOrFail($id);
        $name->delete();

        return response()->json(['message' => 'Counselor deleted successfully!']);
    }

    // The create() and edit() methods are no longer needed as they only returned views.
}