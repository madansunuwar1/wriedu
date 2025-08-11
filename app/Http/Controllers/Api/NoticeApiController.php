<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NoticeApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notices = Notice::all();
        return response()->json($notices);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // This method can be left empty or return a view if you have a separate create page.
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $notice = new Notice();
        $notice->title = $request->title;
        $notice->description = $request->description;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('notices', 'public');
            $notice->image = $imagePath;
        }

        $notice->save();

        return response()->json($notice, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notice = Notice::findOrFail($id);

        // Mark the notice as read if it's being accessed by the authenticated user
        if (Auth::check()) {
            $user = Auth::user();
            if (!$user->seenNotices()->where('notice_id', $notice->id)->exists()) {
                $user->seenNotices()->attach($notice->id, ['seen_at' => Carbon::now()]);
            }

            $notice->read_status = true;
            $notice->read_at = Carbon::now();
            $notice->save();
        }

        return response()->json($notice);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $notice = Notice::findOrFail($id);
        return response()->json($notice);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $notice = Notice::findOrFail($id);
        $notice->title = $request->title;
        $notice->description = $request->description;

        if ($request->hasFile('image')) {
            if ($notice->image && Storage::disk('public')->exists($notice->image)) {
                Storage::disk('public')->delete($notice->image);
            }

            $image = $request->file('image');
            $imagePath = $image->store('notices', 'public');
            $notice->image = $imagePath;
        }

        $notice->save();

        return response()->json($notice);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notice = Notice::findOrFail($id);

        if ($notice->image && Storage::disk('public')->exists($notice->image)) {
            Storage::disk('public')->delete($notice->image);
        }

        $notice->delete();

        return response()->json(null, 204);
    }

    /**
     * Mark a single notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        try {
            $notice = Notice::findOrFail($id);
            $notice->read_status = true;
            $notice->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all notifications (for API purposes).
     *
     * @return \Illuminate\Http\Response
     */
    public function getNotifications()
    {
        try {
            $notices = Notice::orderBy('created_at', 'desc')->get();

            $notifications = $notices->map(function ($notice) {
                return [
                    'id' => $notice->id,
                    'message' => $notice->title,
                    'content' => $notice->description,
                    'time' => $notice->created_at->diffForHumans(),
                    'created_at' => $notice->created_at,
                    'read' => (bool) $notice->read_status
                ];
            });

            $unreadCount = $notices->where('read_status', false)->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'notifications' => $notifications,
                    'unreadCount' => $unreadCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
