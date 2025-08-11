<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\University;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use App\Models\Notice;
use App\Models\Image;
use App\Models\DataEntry;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use Carbon\Carbon; 

class NoticeController extends Controller
{
    protected $notificationController;


    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notices = Notice::all();
        return view('backend.notice.index', compact('notices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.notice.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    try {
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Add calendar event validation
            'add_to_calendar' => 'nullable|boolean',
            'event_start_date' => 'nullable|date_format:Y-m-d',
            'event_end_date' => 'nullable|date_format:Y-m-d|after_or_equal:event_start_date',
            'event_title' => 'nullable|string|max:255',
            'event_color' => 'nullable|string|in:danger,warning,primary,success',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        // Handle the image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('notices', 'public');
            $validatedData['image'] = $imagePath;
        }

        // Create the notice with display period
        $notice = Notice::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'image' => $validatedData['image'],
            'display_start_at' => now(),
            'display_end_at' => now()->addDays(2),
        ]);

        // Create calendar event if requested
        if ($request->has('add_to_calendar') && $request->add_to_calendar) {
            $this->createCalendarEvent($request, $notice);
        }

        $this->notificationController->createNoticeNotification($notice);

        // Commit the transaction
        DB::commit();

        return redirect()->route('backend.notice.index')->with('success', 'Notice created successfully!');
    } catch (\Exception $e) {
        // Rollback the transaction on error
        DB::rollBack();
        \Log::error('Error creating notice: ' . $e->getMessage());
        return redirect()->back()->withErrors('Failed to create notice: ' . $e->getMessage())->withInput();
    }
}



private function createCalendarEvent($request, $notice)
{
    // Use event title if provided, otherwise use notice title
    $eventTitle = $request->event_title ?: $notice->title;
    
    // Use event start date if provided, otherwise use today
    $startDate = $request->event_start_date ?: now()->format('Y-m-d');
    
    // Use event end date if provided, otherwise use start date
    $endDate = $request->event_end_date ?: $startDate;
    
    // Use event color if provided, otherwise use primary
    $eventColor = $request->event_color ?: 'primary';

    // Create the event
    Event::create([
        'title' => $eventTitle,
        'description' => $notice->description, // Use notice description
        'image' => $notice->image, // Use notice description
        'start_date' => $startDate,
        'end_date' => $endDate,
        'color' => $eventColor,
        'notice_id' => $notice->id, // Link to the notice
        'type' => 'notice', // Mark as notice event
    ]);
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $notices = Notice::findOrFail($id);
        return view('backend.notice.update', compact('notices'));
    }
    
   
    
    public function searchcourse()
    {
        $images = Image::all();
        $data_entries = DataEntry::all(); 
        $universities = University::all();
    
        // Pass universities to the view
        return view('backend.notice.searchcourse', compact('data_entries', 'images', 'universities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $notice = Notice::findOrFail($id);
    
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $notice->title = $request->input('title');
        $notice->description = $request->input('description');
    
        if ($request->hasFile('image')) {
            if ($notice->image && Storage::disk('public')->exists($notice->image)) {
                Storage::disk('public')->delete($notice->image);
            }
    
            $image = $request->file('image');
            $imagePath = $image->store('notices', 'public');
            $notice->image = $imagePath;
        }
    
        $notice->save();
    
        return redirect()->route('backend.notice.index')->with('success', 'Notice updated successfully');
    }

    /**
     * Search course functionality
     */
    
    /**
     * Get notifications for the notification dropdown
     */
    public function sampleNotice(): JsonResponse
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
                'notifications' => $notifications,
                'unreadCount' => $unreadCount
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Error in sampleNotice: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch notifications'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            // Add logging to track the request
            \Log::info('Marking all notifications as read');
            
            $affected = Notice::where('read_status', false)
                ->update(['read_status' => true]);
            
            \Log::info('Updated notifications count: ' . $affected);
            
            return response()->json([
                'status' => 'success',
                'message' => 'All notifications marked as read',
                'affected' => $affected
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to mark notifications as read: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead($id): JsonResponse
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
     * Get all notifications (for API purposes)
     */
    public function getNotifications(): JsonResponse
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


    public function showNotifications()
{
    $notices = Notice::orderBy('created_at', 'desc')->get();
    $notifications = $notices->map(function ($notice) {
        return [
            'id' => $notice->id,
            'message' => $notice->title,
            'content' => $notice->description,
            'time' => $notice->created_at->diffForHumans(),
            'read' => (bool) $notice->read_status
        ];
    });
    
    $unreadCount = $notices->where('read_status', false)->count();

    return view('notifications', [
        'notifications' => $notifications,
        'unreadCount' => $unreadCount
    ]);
}



public function check()
{
    $notifications = Notification::where('user_id', auth()->id())
        ->where('read', false)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'message' => $notification->type === 'mention' 
                    ? "{$notification->data['mentioner_name']} mentioned you in a comment"
                    : $notification->data['message'],
                'content' => $notification->type === 'mention'
                    ? $notification->data['comment_text']
                    : null,
                'time' => $notification->created_at->diffForHumans(),
                'read' => $notification->read
            ];
        });

    return response()->json(['notifications' => $notifications]);
}
public function getUnseenNotices()
{
    $user = Auth::user();
    $now = Carbon::now();

    $notices = Notice::where('display_start_at', '<=', $now)
        ->where('display_end_at', '>=', $now)
        ->whereDoesntHave('seenByUsers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->get();

    // Add image URL to the response
    $notices = $notices->map(function ($notice) {
        return [
            'id' => $notice->id,
            'title' => $notice->title,
            'description' => $notice->description,
            'image_url' => $notice->image ? asset('storage/' . $notice->image) : null, // Generate full image URL
            'display_start_at' => $notice->display_start_at,
            'display_end_at' => $notice->display_end_at,
        ];
    });

    return response()->json(['notices' => $notices]);
}

public function markAsSeen($id)
{
    try {
        // Get the authenticated user
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        // Find the notice by ID
        $notice = Notice::find($id);
        if (!$notice) {
            return response()->json(['status' => 'error', 'message' => 'Notice not found'], 404);
        }

        // Check if the user has already seen the notice
        if ($user->seenNotices()->where('notice_id', $notice->id)->exists()) {
            return response()->json(['status' => 'success', 'message' => 'Notice already marked as seen']);
        }

        // Attach the notice to the user's seen notices
        $user->seenNotices()->attach($notice->id, ['seen_at' => Carbon::now()]);

        // Update the notice's read_status and read_at fields
        $notice->read_status = true;
        $notice->read_at = Carbon::now();
        $notice->save();

        return response()->json(['status' => 'success', 'message' => 'Notice marked as seen']);
    } catch (\Exception $e) {
        // Log the error
        \Log::error('Error marking notice as seen: ' . $e->getMessage());

        return response()->json(['status' => 'error', 'message' => 'Failed to mark notice as seen'], 500);
    }
}

 public function universityprofile($id)
    {
        // Fetch the university data using the provided ID
        $university = DataEntry::findOrFail($id); 
    
        // Get the universities collection and find the matching university based on 'newUniversity'
        $universities = University::all();
        $matchedUniversity = $universities->firstWhere('name', $university->newUniversity);
    
        // Dump the matched university data to inspect it
    
        // Pass the university data and matched university to the view
        return view('backend.notice.universityprofile', compact('university', 'universities', 'matchedUniversity'));
    }

    /**
 * Display the specified notice.
 *
 * @param  int  $id
 * @return mixed
 */
public function show($id)
{
    try {
        // Find the notice by ID
        $notice = Notice::findOrFail($id);
        
        // Mark the notice as read if it's being accessed by the authenticated user
        if (Auth::check()) {
            $user = Auth::user();
            // If you're using the seen notices relationship
            if (!$user->seenNotices()->where('notice_id', $notice->id)->exists()) {
                $user->seenNotices()->attach($notice->id, ['seen_at' => Carbon::now()]);
            }
            
            // Update the notice read status
            $notice->read_status = true;
            $notice->read_at = Carbon::now();
            $notice->save();
        }
        
        // Return the view with the notice data
        return view('backend.notice.show', compact('notice'));
    } catch (\Exception $e) {
        \Log::error('Error showing notice: ' . $e->getMessage());
        return redirect()->route('backend.notice.index')->withErrors('Notice not found or could not be displayed.');
    }
}


}