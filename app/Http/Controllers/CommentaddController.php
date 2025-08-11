<?php

namespace App\Http\Controllers;

use App\Models\CommentAdd;
use Illuminate\Http\Request;

class CommentaddController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexcomments()
    {
        $commentAdds = CommentAdd::all();
        return view('backend.application.indexcomments', compact('commentAdds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function comments()
    {
        return view('backend.application.comments');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storecomments(Request $request)
    {
        $request->validate([
            'applications' => 'required|string|max:255',
            
        ]);

        // Store the data in the database
        CommentAdd::create($request->all());

        // Redirect back with success message
        return redirect()->route('backend.application.indexcomments')->with('success', 'comment saved successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Commentadd $commentadd)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $commentAdds = CommentAdd::findOrFail($id);
        return view('backend.application.updatecomments', compact('commentAdds'));

    }
    public function update(Request $request, $id)
    {
        // Find the data entry by ID or fail if not found
        $commentadds = Commentadd::findOrFail($id);
    
        // Validate the request
        $request->validate([
            'applications' => 'required|string|max:255',
            
        ]);
    
        // Update the data entries
        $commentAdds->applications = $request->input('applications');
       
        // Save the updated data entry
        $commentAdds->save();
    
        // Redirect with a success message
        return redirect()->route('backend.application.indexcomments')->with('success', 'comments updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $commentAdds = CommentAdd::findOrFail($id);

        

        // Delete the comment from the database
        $commentAdds->delete();

        // Redirect with success message
        return redirect()->route('backend.application.indexcomments')->with('success', 'application deleted successfully');
    }
}
