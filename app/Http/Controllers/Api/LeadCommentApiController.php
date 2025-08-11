<?php

namespace App\Http\Controllers\Api;

use App\Models\LeadComment;
use App\Models\Enquiry;
use App\Models\Application;
use App\Models\User;
use App\Models\Lead;
use App\Models\CommentMention;
use App\Http\Controllers\Controller;
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


class LeadCommentApiController extends Controller
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





    public function getReminders()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([]);
        }

        $remindersQuery = LeadComment::whereNotNull('date_time')
            ->where('is_completed', false)
            ->with(['lead:id,name', 'user:id,name']);

        // If user is not an admin or a manager, only show their own reminders.
        if (!$user->hasRole(['Administrator', 'Manager'])) {
            $remindersQuery->where('user_id', $user->id);
        }

        $now = Carbon::now();

        $reminders = $remindersQuery->orderBy('date_time', 'asc')->get()
            ->map(function ($comment) use ($now) {
                return [
                    'id' => $comment->id,
                    'lead_id' => $comment->lead_id,
                    'lead_name' => $comment->lead->name ?? 'Unknown Lead',
                    'user_name' => $comment->user->name ?? 'Unknown User',
                    'comment' => $comment->comment,
                    'date_time' => $comment->date_time,
                    'is_completed' => (bool)$comment->is_completed,
                    'is_overdue' => Carbon::parse($comment->date_time)->isPast(),
                ];
            });

        // Note: Sending emails in an API endpoint can slow it down.
        // Consider moving this to a scheduled job for better performance.
        // $this->sendReminderEmails($reminders);

        return response()->json($reminders);
    }

    /**
     * Mark a reminder as complete. This will be our API endpoint.
     */
    public function markComplete($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
            }

            $comment = LeadComment::with(['lead:id,name', 'user:id,name'])->findOrFail($id);

            // You might want to add a permission check here
            // if (!$user->isAdmin() && $comment->user_id !== $user->id) {
            //     return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            // }

            $comment->is_completed = true;
            $comment->completed_by = $user->id;
            $comment->completed_at = now();
            $comment->save();

            // Logic for sending completion emails can remain here if desired.

            return response()->json([
                'success' => true,
                'message' => 'Reminder marked as complete.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark reminder as complete', ['error' => $e->getMessage(), 'reminder_id' => $id]);
            return response()->json(['success' => false, 'message' => 'Failed to mark reminder as complete.'], 500);
        }
    }

    // In app/Http/Controllers/Api/LeadCommentApiController.php

    public function store(Request $request)
    {
        // 1. VALIDATION: Adjusted for your Vue component's payload.
        // 'application_id' is now the required field.
        $validated = $request->validate([
            'comment'        => 'required|string|max:2000',
            'comment_type'   => 'required|string|max:255',
            'application_id' => 'required|integer|exists:applications,id',
            // Optional fields
            'lead_id'        => 'nullable|integer|exists:leads,id',
            'enquiry_id'     => 'nullable|integer|exists:enquiries,id',
            'mentioned_users' => 'nullable|string', // Assuming it's a JSON string of IDs
            'date_time'      => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // 2. DATA PREPARATION: Build the array for creating the comment.
            // We use the $validated data for security and consistency.
            $commentData = [
                'comment'        => $validated['comment'],
                'comment_type'   => $validated['comment_type'],
                'application_id' => $validated['application_id'],
                'user_id'        => auth()->id(),
                'author_name'    => auth()->user()->name, // For convenience, though user_id is better
            ];

            // Conditionally add optional fields if they were provided and validated
            if (isset($validated['lead_id'])) {
                $commentData['lead_id'] = $validated['lead_id'];
            }
            if (isset($validated['enquiry_id'])) {
                $commentData['enquiry_id'] = $validated['enquiry_id'];
            }
            if (isset($validated['date_time'])) {
                $commentData['date_time'] = $validated['date_time'];
            }

            // 3. CREATION: Create the comment record.
            $comment = LeadComment::create($commentData);

            // 4. ACTIVITY LOGGING (Optional but recommended)
            $application = Application::find($validated['application_id']);
            activity()
                ->causedBy(auth()->user())
                ->performedOn($application) // Log against the parent application
                ->withProperties([
                    'action'       => 'comment_added',
                    'comment_body' => Str::limit($validated['comment'], 100),
                    'comment_id'   => $comment->id,
                ])
                ->log("added a comment to application '{$application->name}'");

            // ... Your mention processing logic can go here if needed ...

            DB::commit();

            // 5. RESPONSE PREPARATION: Eager load the 'user' relationship.
            // Your Vue component uses `comment.user.name` and `comment.user.avatar`.
            $comment->load('user');

            // 6. RETURN JSON RESPONSE: Send the success response back to Vue.
            return response()->json([
                'success' => true,
                'message' => 'Comment posted successfully.',
                'comment' => $comment // The key is 'comment', matching your Vue component's expectation.
            ], 201); // 201 Created status code

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to store comment', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An unexpected server error occurred while posting the comment.'
            ], 500);
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

            activity()
                ->causedBy(auth()->user())
                ->performedOn($comment->lead ?? $comment)
                ->withProperties([ // <-- Add this block
                    'action'       => 'comment_updated',
                    'old_comment'  => $oldCommentBody,
                    'new_comment'  => $validated['comment'],
                    'comment_id'   => $comment->id,
                ])
                ->log("updated a comment");

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
     * Store a new comment via a pure API request and return JSON.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\JsonResponse
     */

    public function storeApi(Request $request, Lead $lead)
    {
        // 1. Validate ALL incoming data from Vue, including the optional date_time.
        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'date_time' => 'nullable|date', // <-- ADD THIS VALIDATION RULE
        ]);

        // 2. Prepare the data for creation.
        $commentData = [
            'comment' => $validated['comment'],
            'user_id' => auth()->id(),
            'author_name' => auth()->user()->name,
        ];

        // 3. Conditionally add date_time if it exists in the validated data.
        if (!empty($validated['date_time'])) {
            $commentData['date_time'] = $validated['date_time'];
        }

        // 4. Create the comment using the complete data array.
        $comment = $lead->lead_comments()->create($commentData);
        activity()
            ->causedBy(auth()->user())
            ->performedOn($lead) // Log against the parent Lead
            ->withProperties([
                'action'       => 'comment_added',
                'comment_body' => $validated['comment'],
                'comment_id'   => $comment->id,
            ])
            ->log("commented");
        // 5. Eager load the 'user' relationship for the response.
        // Let's also load the relationship your old response used, just in case.
        $comment->load('createdBy'); // 'created_by' is what your Vue component is expecting.

        // 6. Return a clean JSON response that Vue can easily use.
        return response()->json([
            'success' => true,
            'message' => 'Comment posted successfully.',
            'comment' => $comment, // The comment object will now include 'date_time' if it was set.
        ], 201);
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
