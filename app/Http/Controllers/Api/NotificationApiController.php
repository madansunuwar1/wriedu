<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\LeadComment;
use App\Models\CommentMention;
use App\Events\UserMentioned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Notice;
use Illuminate\Support\Facades\Log;

class NotificationApiController extends Controller
{
    /**
     * Get user notifications with pagination and detailed information
     */
    public function getNotifications()
{
    try {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($notification) {
                // Properly decode the data regardless of how it was stored
                $data = is_string($notification->data) ? json_decode($notification->data, true) : ($notification->data ?? []);
                
                \Log::info('Notification Data:', ['data' => $data]); // Log the data properly
                
                // Construct the response with more flexible access to data
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'message' => $notification->message ?? ($data['message'] ?? ''),
                    'content' => $notification->content ?? ($data['content'] ?? ''),
                    'link' => $notification->link ?? ($data['link'] ?? ''),
                    'read' => $notification->read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'title' => $data['title'] ?? null,
                    'notification_type' => $data['notification_type'] ?? null,
                    'comment_id' => $data['comment_id'] ?? null,
                    'author_id' => $data['author_id'] ?? null
                ];
            });

        return response()->json($notifications);
    } catch (\Exception $e) {
        \Log::error('Error fetching notifications: ' . $e->getMessage());
        return response()->json(['error' => 'Internal Server Error'], 500);
    }
}

    /**
     * Create notifications for a new comment
     */
        /**
     * Create notifications for a new comment
     */
    public function createCommentNotifications($comment, $mentionedUsers = [], $context = 'leadform')
{
    try {
        DB::beginTransaction();

        // Get mentioned user IDs
        $mentionedUserIds = collect($mentionedUsers)->pluck('id')->toArray();

        // REMOVE THIS SECTION - Don't create general notifications for non-mentioned users
        // Only keep the code for creating mention notifications below

        // Create mention notifications for the mentioned users
        foreach ($mentionedUsers as $user) {
            // Skip if user is mentioning themselves
            if ($user->id === auth()->id()) {
                continue;
            }

            $this->createNotification([
                'user_id' => $user->id,
                'message' => auth()->user()->name . ' mentioned you in a comment',
                'content' => Str::limit($comment->comment, 100),
                'link' => $this->generateNotificationLink($context, $comment),
                'type' => 'mention',
                'data' => [
                    'comment_id' => $comment->id,
                    'author_id' => auth()->id(),
                    'notification_type' => 'mention',
                    'context' => $context // Add context for filtering notifications
                ]
            ]);
        }

        DB::commit();
        return true;
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating notifications: ' . $e->getMessage());
        throw $e;
    }
}
/**
 * Generate the notification link based on the context.
 *
 * @param string $context
 * @param object $comment
 * @return string
 */
protected function generateNotificationLink($context, $comment)
{
    switch ($context) {
        case 'leadform':
            return '/leadform/records/' . $comment->lead_id;
        case 'enquiry':
            return '/enquiry/records/' . $comment->enquiry_id;
        default:
            return '/';
    }
}

    /**
     * Create a notification for a notice
     */
    public function createNoticeNotification($notice)
    {
        try {
            DB::beginTransaction();

            // Notify all users except the author about the new notice
            $users = User::where('id', '!=', auth()->id())->get();

            foreach ($users as $user) {
                $this->createNotification([
                    'user_id' => $user->id,
                    'message' => 'New notice: ' . $notice->title,
                    'content' => Str::limit($notice->description, 100),
                    'link' => '/notice/show/' . $notice->id,
                    'type' => 'notice',
                    'data' => [
                        'notice_id' => $notice->id,
                        'author_id' => auth()->id(),
                        'notification_type' => 'general'
                    ]
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating notice notifications: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Create notifications for a new notice
     * 
     * @param \App\Models\Notice $notice
     * @return void
     */
   


      /**
     * Create notifications for comment mentions
     */
    public function createMentionNotifications($comment, array $mentionedUsers)
{
    try {
        DB::beginTransaction();

        foreach ($mentionedUsers as $user) {
            if ($user->id === auth()->id()) {
                continue;
            }

            $this->createNotification([
                'user_id' => $user->id,
                'message' => e(auth()->user()->name) . ' mentioned you in a comment',
                'content' => e($comment->comment),
                'link' => '/leadcomment/' . $comment->id,
                'type' => 'mention',
                'data' => [  // Pass as array, createNotification will handle JSON conversion
                    'comment_id' => $comment->id,
                    'author_id' => auth()->id(),
                    'notification_type' => 'mention',
                    'mentioner_name' => auth()->user()->name,
                    'comment_text' => $comment->comment
                ]
            ]);

            CommentMention::create([
                'comment_id' => $comment->id,
                'mentioned_user_id' => $user->id,
                'mentioner_id' => auth()->id(),
                'user_name' => $user->name
            ]);

            event(new UserMentioned($user, $comment));
        }

        DB::commit();
        return true;
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error creating mention notifications: ' . $e->getMessage());
        throw $e;
    }
}


    public function getUnreadNotifications()
    {
        return Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                // Handle both array and JSON string data
                $data = is_string($notification->data) ? json_decode($notification->data, true) : ($notification->data ?? []);
                
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'message' => $notification->message,
                    'content' => $notification->content,
                    'time' => $notification->created_at->diffForHumans(),
                    'read' => $notification->read,
                    'data' => $data // Include the decoded data if needed
                ];
            });
    }


    /**
     * Create a single notification
     */
    public function createNotification($data)
    {
        // Ensure all required fields are present
        $requiredFields = ['user_id', 'message', 'type'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }
    
        // Convert data to JSON if it's an array
        $dataField = isset($data['data']) ? 
            (is_array($data['data']) ? json_encode($data['data']) : $data['data']) 
            : null;
    
        // Create the notification
        return Notification::create([
            'user_id' => $data['user_id'],
            'message' => $data['message'],
            'content' => $data['content'] ?? null,
            'link' => $data['link'] ?? null,
            'read' => false,
            'type' => $data['type'],
            'data' => $dataField
        ]);
    }

    /**
     * Mark a specific notification as read
     */
         public function markAsRead(Notification $id)
    {
        try {
            // The $id variable is now the fully loaded Notification model.
            $notification = $id;

            // Security check: Ensure the found notification belongs to the authenticated user.
            if ($notification->user_id !== auth()->id()) {
                // This is a forbidden action, not a "not found" error.
                return response()->json(['success' => false, 'message' => 'This action is unauthorized.'], 403);
            }

            if ($notification->read) {
                return response()->json(['success' => true, 'message' => 'Notification is already marked as read']);
            }

            $notification->read = true;
            $notification->save();

            return response()->json(['success' => true, 'message' => 'Notification marked as read']);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage(), [
                'notification_id' => $id->id ?? 'unknown',
                'exception' => $e
            ]);
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }


    /**
     * Mark all notifications as read for the authenticated user
     */
    public function markAllAsRead()
{
    try {
        // Ensure the user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $affectedRows = Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->update(['read' => true]);

        if ($affectedRows === 0) {
            return response()->json([
                'success' => true,
                'message' => 'No unread notifications to mark as read'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error marking all notifications as read: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'exception' => $e
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to mark notifications as read: ' . $e->getMessage()
        ], 500);
    }
}



    /**
 * Display the specified notice.
 *
 * @param  int  $id
 * @return mixed
 */
/**
 * Display the specified notification target and redirect to original page.
 *
 * @param  int  $id
 * @return mixed
 */
public function showFromNotification($notification_id) {
    try {
        // Find the notification by ID
        $notification = Notification::findOrFail($notification_id);
        
        // Mark the notification as read
        if (Auth::check()) {
            $notification->read = true;
            $notification->save();
        }
        
        // Decode the notification data to get redirect information
        $data = is_string($notification->data) ? json_decode($notification->data, true) : ($notification->data ?? []);
        
        // Use the link property from the notification which should contain the original page URL
        if (!empty($notification->link)) {
            return redirect($notification->link);
        }
        
        // If there's a source_url in the notification, use that
        if (!empty($notification->source_url)) {
            return redirect($notification->source_url);
        }
        
        // For comment notifications, redirect to the lead page
        if ($notification->type == 'comment' || $notification->type == 'mention') {
            $comment_id = $data['comment_id'] ?? null;
            if ($comment_id) {
                $comment = LeadComment::find($comment_id);
                if ($comment) {
                    // Redirect to the lead page that contains this comment
                    return redirect('/leads/' . $comment->lead_id . '#comment-' . $comment_id);
                }
            }
        } 
        // For notice notifications
        elseif ($notification->type == 'notice') {
            $notice_id = $data['notice_id'] ?? null;
            if ($notice_id) {
                // Redirect to the notices page with the specific notice highlighted
                return redirect('/notices#notice-' . $notice_id);
            }
        }
        
        // Fallback to dashboard if no redirection target is found
        return redirect()->route('backend.dashboard');
    } catch (\Exception $e) {
        \Log::error('Error processing notification: ' . $e->getMessage());
        return redirect()->route('backend.dashboard')->withErrors('Could not navigate to the notification source.');
    }
}


/**
 * Create notifications for forwarded documents
 * 
 * @param mixed $lead The lead being forwarded
 * @param mixed $recipient The user receiving the lead
 * @param string $notes Additional notes for the forwarding
 * @param int $applicationId The ID of the created application (if applicable)
 * @return bool
 */
public function createForwardedDocumentNotification($lead, $recipient, $notes = null, $applicationId = null)
{
    try {
        DB::beginTransaction();

        // Create notification for the recipient
        $this->createNotification([
            'user_id' => $recipient->id,
            'message' => auth()->user()->name . ' forwarded a document to you',
            'content' => 'Lead #' . $lead->id . ': ' . $lead->name . ($notes ? ' - ' . Str::limit($notes, 100) : ''),
            'link' => $applicationId ? '/app/applications/record/'. $applicationId : '/leadform/records/' . $lead->id,
            'type' => 'document_forwarded',
            'data' => [
                'lead_id' => $lead->id,
                'author_id' => auth()->id(),
                'recipient_id' => $recipient->id,
                'application_id' => $applicationId,
                'notification_type' => 'document_forwarded'
            ]
        ]);
        DB::commit();
        return true;
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error creating forwarded document notification: ' . $e->getMessage());
        throw $e;
    }
}
}