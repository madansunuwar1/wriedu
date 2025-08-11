<?php

namespace App\Http\Controllers;
use App\Models\DataEntry;
use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = Image::all();
        $data_entries = DataEntry::all();
        return view('backend.image.index', compact('images', 'data_entries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.image.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
          
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Handle the file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('data', 'public'); // Store image in 'storage/app/public/notices'
            $validatedData['image'] = $imagePath; // Save the file path in the database
        }
    
        // Store the data in the database
        try {
            Image::create($validatedData);
        } catch (\Exception $e) {
            // Log and handle any exceptions during database operations
            \Log::error('Failed to store notice: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to save the image. Please try again.');
        }
    
        // Redirect back with success message
        return redirect()->route('backend.image.index')->with('success', 'Notice saved successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        //
    }


    public function session()
    {
        return view('backend.script.session');
    }

    public function alert()
    {
        return view('backend.script.alert');
    }

    public function notification()
    {
        return view('backend.script.notification');
    }

    public function navbar()
    {
        return view('backend.script.navbar');
    }


    public function media()
    {
        return view('backend.script.media');
    }

    public function pagination()
    {
        return view('backend.script.pagination');
    }
}
