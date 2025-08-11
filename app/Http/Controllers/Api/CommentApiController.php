<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommentAdd;
use Illuminate\Http\Request;

class CommentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $commentAdds = CommentAdd::all();
        return response()->json($commentAdds);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'applications' => 'required|string|max:255',
        ]);

        $commentAdd = CommentAdd::create($request->all());

        return response()->json($commentAdd, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'applications' => 'required|string|max:255',
        ]);

        $commentAdd = CommentAdd::findOrFail($id);
        $commentAdd->update($request->all());

        return response()->json($commentAdd, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $commentAdd = CommentAdd::findOrFail($id);
        $commentAdd->delete();

        return response()->json(null, 204);
    }
}
