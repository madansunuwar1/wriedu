<?php

namespace App\Http\Controllers;

use App\Models\CommentPass;
use Illuminate\Http\Request;

class CommentPassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comment_passes = CommentPass::all();
        return view('backend.enquiryhistory.index', compact('comment_passes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.enquiryhistory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // Validate the incoming data
         $request->validate([
            'comment' => 'required|string|max:255',
           
        ]);

        // Store the data in the database
        CommentPass::create($request->all());

        // Redirect back with success message
        return redirect()->route('backend.enquiryhistory.index')->with('success', 'Comment saved successfully!');
    }

   

    public function edit($id)
    {
        $comment_passes = CommentPass::findOrFail($id);
        return view('backend.enquiryhistory.update', compact('comment_passes'));

    }
    public function update(Request $request, $id)
{
    // Find the data entry by ID or fail if not found
    $comment_passes = CommentPass::findOrFail($id);

    // Validate the request
    $request->validate([
        'comment' => 'required|string|max:255',
    ]);

    // Update the data entries
    $comment_passes->comment = $request->input('comment');
   
    // Save the updated data entry
    $comment_passes->save();

    // Redirect with a success message
    return redirect()->route('backend.enquiryhistory.index')->with('success', ' Comment updated successfully');
}

  
}
