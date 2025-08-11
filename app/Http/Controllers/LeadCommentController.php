<?php

namespace App\Http\Controllers;

use App\Models\LeadComment;
use App\Models\User;
use App\Models\CommentMention;

use App\Events\UserMentioned;  // Make sure this line is added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Service\CommentHandler; 
use App\Notifications\MentionNotification;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\NotificationController;


class LeadCommentController extends Controller 
{
    private $commentHandler;

    protected $noticeController;

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
    // Example condition: Check if the user is an admin
    if (Auth::user()->isAdmin()) {
        // Fetch all lead comments ordered by creation date
        $lead_comments = LeadComment::orderBy('created_at', 'desc')->get();

        // Return the admin-specific view
        return view('backend.enquiryhistory.index', compact('lead_comments'));
    } else {
        // Fetch only the lead comments created by the current user
        $lead_comments = LeadComment::where('user_id', Auth::id())
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        // Return the user-specific view
        return view('backend.leadcomment.index', compact('lead_comments'));
    }
}


    /**
     * Show the form for creating a new resource.
     */
    public function create($type)
{
    if ($type == 'enquiryhistory') {
        return view('backend.enquiryhistory.create');
    } elseif ($type == 'leadcomment') {
        return view('backend.leadcomment.create');
    } else {
        // Handle the case where the type is not recognized
        return redirect()->back()->withErrors('Invalid type specified.');
    }
}



  

    /**
     * Store a newly created resource in storage.
     */
    public function getReminders()
    {
        // Get comments with date_time that have not been completed
        $reminders = LeadComment::where('date_time', '!=', null)
            ->where('is_completed', false)
            ->with(['lead:id,name', 'user:id,name'])
            ->get()
            ->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'lead_id' => $comment->lead_id,
                    'lead_name' => $comment->lead->name,
                    'user_id' => $comment->user_id,
                    'user_name' => $comment->user->name,
                    'comment' => $comment->comment,
                    'date_time' => $comment->date_time,
                    'is_completed' => (bool)$comment->is_completed
                ];
            });
            
        return response()->json($reminders);
    }

    /**
     * Mark a reminder as complete
     */
    public function markComplete($id)
    {
        $comment = LeadComment::findOrFail($id);
        $comment->is_completed = true;
        $comment->save();
        
        return response()->json(['success' => true]);
    }
     
    public function store(Request $request)
{
    $request->validate([
        'comment' => 'required|string|max:255',
        'lead_id' => 'nullable|exists:leads,id',
        'comment_type'=> 'nullable|string|max:255',
        'enquiry_id' => 'nullable|exists:enquiries,id',
        'application_id' => 'nullable|exists:applications,id',
        'mentioned_users' => 'nullable|string',
        'date_time' => 'nullable|date_format:Y-m-d H:i:s',
    ]);

    try {
        DB::beginTransaction();

        $commentData = [
            'comment' => $request->input('comment'),
            'author_name' => auth()->user()->name,
            'user_id' => auth()->id(),
            'comment_type'=> $request->input('comment_type'),
        ];

        if ($request->has('lead_id')) {
            $commentData['lead_id'] = $request->input('lead_id');
        }

        if ($request->has('enquiry_id')) {
            $commentData['enquiry_id'] = $request->input('enquiry_id');
        }

        if ($request->has('application_id')) {
            $commentData['application_id'] = $request->input('application_id');
        }
        
        // Add date_time to the comment data if it's provided in the request
        if ($request->has('date_time')) {
            $commentData['date_time'] = $request->input('date_time');
        } else {
            // Default to current date and time if not provided
            $commentData['date_time'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        $comment = LeadComment::create($commentData);

        // Enhanced activity logging with detailed information
        $logData = [
            'action' => 'created_comment',
            'comment' => $comment->comment,
            'comment_type' => $comment->comment_type,
            'user_name' => auth()->user()->name,
            'comment_id' => $comment->id,
        ];

        // Determine the event type and related entity name
        $eventType = '';
        $entityName = '';

        if ($comment->lead_id) {
            $eventType = 'Lead';
            $lead = \App\Models\Lead::find($comment->lead_id);
            $entityName = $lead ? $lead->name : 'Unknown Lead';
            $logData['lead_id'] = $comment->lead_id;
            $logData['lead_name'] = $entityName;
        } elseif ($comment->enquiry_id) {
            $eventType = 'Enquiry';
            $enquiry = \App\Models\Enquiry::find($comment->enquiry_id);
            $entityName = $enquiry ? ($enquiry->name ?? $enquiry->title ?? 'Enquiry #' . $enquiry->id) : 'Unknown Enquiry';
            $logData['enquiry_id'] = $comment->enquiry_id;
            $logData['enquiry_name'] = $entityName;
        } elseif ($comment->application_id) {
            $eventType = 'Application';
            $application = \App\Models\Application::find($comment->application_id);
            $entityName = $application ? ($application->name ?? $application->title ?? 'Application #' . $application->id) : 'Unknown Application';
            $logData['application_id'] = $comment->application_id;
            $logData['application_name'] = $entityName;
        } else {
            $eventType = 'General';
            $entityName = 'System';
        }

        $logData['event_type'] = $eventType;
        $logData['entity_name'] = $entityName;

        // Create the log message
        $logMessage = auth()->user()->name . ' commented on ' . $eventType . ': ' . $entityName;

        activity()
            ->causedBy(auth()->user())
            ->performedOn($comment)
            ->withProperties($logData)
            ->log($logMessage);

        // Additional Laravel log for debugging
        Log::info('Comment Created', [
            'user_name' => auth()->user()->name,
            'event_type' => $eventType,
            'entity_name' => $entityName,
            'comment_id' => $comment->id,
            'comment_type' => $comment->comment_type,
            'comment_preview' => Str::limit($comment->comment, 50),
            'date_time' => $comment->date_time
        ]);

        // Process mentioned users if any
        $mentionedUsers = $request->input('mentioned_users');
        if (!empty($mentionedUsers)) {
            $mentionedUserIds = $this->processMentionedUserIds($mentionedUsers);
            if (!empty($mentionedUserIds)) {
                $users = $this->getUserModels($mentionedUserIds);
                
                foreach ($users as $user) {
                    // Create mention record
                    CommentMention::create([
                        'comment_id' => $comment->id,
                        'mentioned_user_id' => $user->id,
                        'mentioner_user_id' => auth()->id()
                    ]);

                    // Send notification
                    try {
                        $user->notify(new MentionNotification($comment, auth()->user()));
                        
                        // Fire mention event
                        event(new UserMentioned($user, $comment, auth()->user()));
                        
                        Log::info('User mentioned in comment', [
                            'mentioned_user' => $user->name,
                            'comment_id' => $comment->id,
                            'mentioner' => auth()->user()->name
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send mention notification', [
                            'user_id' => $user->id,
                            'comment_id' => $comment->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        DB::commit();
        $comment->load('creator'); 
        return $this->handleSuccess($request, $comment);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error in store method', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);
        return $this->handleError($request, $e->getMessage());
    }
}



     private function processMentionedUserIds($mentionedUsers)
     {
         if (is_string($mentionedUsers)) {
             return json_decode($mentionedUsers, true) ?? [];
         }
         return is_array($mentionedUsers) ? $mentionedUsers : [];
     }
 
     private function getUserModels(array $userIds): array
     {
         return User::whereIn('id', $userIds)->get()->all();
     }





        


     private function handleSuccess(Request $request, LeadComment $comment)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => [
                    'comment' => $comment,
                    'author_name' => auth()->user()->name
                ]
            ]);
        }

        return redirect()->route('backend.leadform.records')
            ->with('success', 'Comment saved successfully!');
    }


    private function handleError(Request $request, $error)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => is_array($error) ? implode(', ', $error) : 'Failed to add comment: ' . $error
            ], 422);
        }

        return redirect()->back()
            ->with('error', 'Failed to save comment')
            ->withInput();
    }
    

    public function edit($id)
    {
        $lead_comment = LeadComment::findOrFail($id);

        if (request()->ajax()) {
            return response()->json(['comment' => $lead_comment]);
        }

        return view('backend.leadcomment.edit', compact('lead_comment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $comment = LeadComment::findOrFail($id);

            // Check permission
            if (!$this->canEditComment($comment)) {
                return $this->unauthorizedResponse($request);
            }

            // Validate the request
            $validated = $request->validate([
                'comment' => 'required|string|max:1000',
                'lead_id' => 'required|exists:leads,id',
            ]);

            // Update the comment
            $comment->update([
                'comment' => $validated['comment'],
                'lead_id' => $validated['lead_id'], 
                'updated_by' => Auth::id(),
                'editor_name' => Auth::user()->name
            ]);

            // Handle AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Comment updated successfully',
                    'editor_name' => Auth::user()->name
                ]);
            }

            // Handle regular form submission
            return redirect()->route('backend.leadcomment.index')
                ->with('success', 'Comment updated successfully');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update comment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update comment')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $comment = LeadComment::findOrFail($id);
            
            if (!$this->canEditComment($comment)) {
                return $this->unauthorizedResponse(request());
            }

            $comment->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Comment deleted successfully'
                ]);
            }

            return redirect()->route('backend.leadcomment.index')
                ->with('success', 'Comment deleted successfully');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete comment'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete comment');
        }
    }

    /**
     * Check if user can edit the comment
     */
    private function canEditComment($comment)
    {
        $user = Auth::user();
        return $user->name === 'admin' || 
               $user->name === 'Admin' || 
               $user->id === $comment->user_id;
    }

    /**
     * Return unauthorized response
     */
    private function unauthorized(Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to modify this comment'
            ], 403);
        }

        return redirect()->back()
            ->with('error', 'You do not have permission to modify this comment');
    }

   


    public function markAsRead(Request $request, $id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }


    public function markAllAsRead(Request $request)
    {
        try {
            auth()->user()->unreadNotifications->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read'
            ], 500);
        }
    }


    public function getNotifications(Request $request)
    {
        try {
            $notifications = auth()->user()
                ->notifications()
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'data' => $notification->data,
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications'
            ], 500);
        }
    }


private function canModifyComment(LeadComment $comment): bool
    {
        $user = Auth::user();
        return $user->isAdmin() || $user->id === $comment->user_id;
    }
}