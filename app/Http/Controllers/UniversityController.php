<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\University;
use App\Models\DataEntry;


class UniversityController extends Controller
{
    public function index()
    {
        // Fetch all universities from the database
        $universities = University::all();

        // Return the view with the universities data
        return view('backend.dataentry.universities', compact('universities'));
    }

    public function store(Request $request)
    {
        // Validate the input data
        $request->validate([
            'name' => 'required|string|max:255',
            'image_link' => 'required|url',
            'background_image' => 'nullable|url', // Added background_image validation
        ]);

        // Create a new university record
        University::create([
            'name' => $request->name,
            'image_link' => $request->image_link,
            'background_image' => $request->background_image, // Save background image
        ]);

        // Redirect back to the universities list with a success message
        return redirect()->route('backend.dataentry.universities')->with('success', 'University added successfully!');
    }

    public function edit($id)
    {
        // Find the university by ID
        $editUniversity = University::findOrFail($id);
        
        // Get all universities for the table
        $universities = University::all();
        
        // Return the view with both the edit university and all universities
        return view('backend.dataentry.universities', compact('editUniversity', 'universities'));
    }

    public function update(Request $request, $id)
    {
        // Validate the input data
        $request->validate([
            'name' => 'required|string|max:255',
            'image_link' => 'required|url',
            'background_image' => 'nullable|url', // Added background_image validation
        ]);

        // Find the university
        $university = University::findOrFail($id);
        
        // Update the university record
        $university->update([
            'name' => $request->name,
            'image_link' => $request->image_link,
            'background_image' => $request->background_image, // Update background image
        ]);

        // Redirect back to the universities list with a success message
        return redirect()->route('backend.dataentry.indexs')->with('success', 'University updated successfully!');
    }

    public function destroy($id)
    {
        // Find the university
        $university = University::findOrFail($id);
        
        // Delete the university
        $university->delete();

        // Redirect back to the universities list with a success message
        return redirect()->route('backend.dataentry.universities')->with('success', 'University deleted successfully!');
    }
}