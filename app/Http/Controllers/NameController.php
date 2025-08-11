<?php

namespace App\Http\Controllers;

use App\Models\Name;
use Illuminate\Http\Request;

class NameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $names = Name::all();
        return view('backend.name.index', compact('names'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.name.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
           
        ]);

        // Store the data in the database
        Name::create($request->all());

        // Redirect back with success message
        return redirect()->route('backend.name.index')->with('success', 'Counselor saved successfully!');
    }



   

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $names = Name::findOrFail($id);
        return view('backend.name.update', compact('names'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the data entry by ID or fail if not found
        $names = Name::findOrFail($id);
    
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        // Update the data entries
        $names->name = $request->input('name');
       
        // Save the updated data entry
        $names->save();
    
        // Redirect with a success message
        return redirect()->route('backend.name.index')->with('success', 'Counselor updated successfully');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $names = Name::findOrFail($id);

        

        // Delete the comment from the database
        $names->delete();

        // Redirect with success message
        return redirect()->route('backend.name.index')->with('success', 'Counselor deleted successfully');
    }
}
