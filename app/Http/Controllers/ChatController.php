<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Events\NewMessage;
use App\Events\UserTyping;
use App\Events\MessageDelivered;
use App\Events\MessageSeen;
use App\Events\MessageStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

class ChatController extends Controller
{
    public function showCalendar()
{
    return view('backend.calendar.index');
}
    /**
     * Get list of users with latest message
     */
    public function showChatWidget()
    {
        $currentUser = Auth::user();
        
        return view('components.chat-widget', [
            'currentUser' => $currentUser
        ]);
    }
    /**
 * Get list of users with latest message
 */
public function getUsers()
{
    $currentUser = Auth::user();

    // Get users excluding the current user with all needed fields
    $users = User::where('id', '!=', $currentUser->id)
        ->select('id', 'name', 'last', 'avatar', 'gender')
        ->get();

    // Format the response with unread counts and latest message
    $formattedUsers = $users->map(function($user) use ($currentUser) {
        // Calculate unread count
        $unreadCount = Message::where('from_user_id', $user->id)
            ->where('to_user_id', $currentUser->id)
            ->where('read', false)
            ->count();

        // Get latest message between users
        $latestMessage = Message::where(function($query) use ($currentUser, $user) {
            $query->where('from_user_id', $currentUser->id)
                  ->where('to_user_id', $user->id);
        })->orWhere(function($query) use ($currentUser, $user) {
            $query->where('from_user_id', $user->id)
                  ->where('to_user_id', $currentUser->id);
        })
        ->latest()
        ->first();

        // Build the correct avatar path
        $avatarPath = $user->avatar 
            ? asset('storage/avatars/' . basename($user->avatar))
            : asset('assets/images/profile/user-1.jpg');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'last' => $user->last,
            'avatar' => $avatarPath,
            'gender' => $user->gender,
            'unread' => $unreadCount,
            'lastMessage' => $latestMessage ? $latestMessage->message : '',
            'lastMessageTime' => $latestMessage ? Carbon::parse($latestMessage->created_at)->format('g:i A') : '',
            'online' => $this->isUserOnline($user->id),
        ];
    });

    // Update current user's online status
    $this->updateUserOnlineStatus($currentUser->id);

    return response()->json($formattedUsers);
}

    /**
     * Get conversation with a specific user
     */
    public function getConversation($userId)
    {
        $currentUser = Auth::user();
        
        // Get messages (unchanged)
        $messages = Message::where(function($query) use ($currentUser, $userId) {
            $query->where('from_user_id', $currentUser->id)
                  ->where('to_user_id', $userId);
        })->orWhere(function($query) use ($currentUser, $userId) {
            $query->where('from_user_id', $userId)
                  ->where('to_user_id', $currentUser->id);
        })
        ->orderBy('created_at')
        ->get();
    
        // Get user details with corrected avatar URLs
        $otherUser = User::find($userId);
        
        // Fix avatar URLs at the source
        $fixAvatarUrl = function($avatar) {
            if (!$avatar) return null;
            
            // If already a full URL with storage path, return as-is
            if (str_contains($avatar, '/storage/avatars/')) {
                return $avatar;
            }
            
            // If just a filename, build full storage URL
            if (!filter_var($avatar, FILTER_VALIDATE_URL)) {
                return asset('storage/avatars/' . basename($avatar));
            }
            
            // If full URL but missing storage path, insert it
            $parsed = parse_url($avatar);
            $path = $parsed['path'] ?? '';
            
            if (!str_contains($path, '/storage/avatars/')) {
                $filename = basename($path);
                return $parsed['scheme'] . '://' . $parsed['host'] 
                     . (isset($parsed['port']) ? ':' . $parsed['port'] : '')
                     . '/storage/avatars/' . $filename;
            }
            
            return $avatar;
        };
    
        $otherUserAvatar = $fixAvatarUrl($otherUser->avatar) 
            ?: asset('assets/images/profile/user-1.jpg');
    
        $currentUserAvatar = $fixAvatarUrl($currentUser->avatar) 
            ?: asset('assets/images/profile/user-1.jpg');
    
        // Format response (unchanged)
        $formattedMessages = $messages->map(function($message) use ($currentUser, $currentUserAvatar, $otherUserAvatar) {
            $isCurrentUser = $message->from_user_id === $currentUser->id;
            $senderName = $isCurrentUser ? $currentUser->name : User::find($message->from_user_id)->name;
            $senderAvatar = $isCurrentUser ? $currentUserAvatar : $otherUserAvatar;
    
            return [
                'id' => $message->id,
                'message' => $message->message,
                'file_path' => $message->file_path,
                'sender' => $isCurrentUser ? 'agent' : 'user',
                'sender_name' => $senderName,
                'sender_avatar' => $senderAvatar,
                'timestamp' => Carbon::parse($message->created_at)->format('g:i A'),
                'status' => $message->status,
            ];
        });
    
        // Mark messages as read (unchanged)
        Message::where('from_user_id', $userId)
            ->where('to_user_id', $currentUser->id)
            ->where('read', false)
            ->update(['read' => true]);
    
        $this->updateUserOnlineStatus($currentUser->id);
    
        return response()->json([
            'messages' => $formattedMessages,
            'current_user_avatar' => $currentUserAvatar,
            'other_user_avatar' => $otherUserAvatar
        ]);
    }
    
    public function sendMessage(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
            'file' => 'nullable|file|mimes:png,jpg,jpeg,gif,xlsx,xls,docx,doc,pdf|max:2048',
        ]);
    
        $currentUser = Auth::user();
        $toUserId = $request->to_user_id;
        $messageText = $request->message;
        $file = $request->file('file');
    
        $message = new Message();
        $message->from_user_id = $currentUser->id;
        $message->to_user_id = $toUserId;
        $message->message = $messageText;
        $message->read = false;
        $message->status = 'sent';
    
        if ($file) {
            $filePath = $file->store('chat_files', 'public');
            $message->file_path = $filePath;
        }
    
        $message->save();
    
        // Get sender's avatar - fixed path handling
        $senderAvatar = $currentUser->avatar 
            ? (filter_var($currentUser->avatar, FILTER_VALIDATE_URL) ? $currentUser->avatar : asset($currentUser->avatar))
            : asset('assets/images/profile/user-1.jpg');
    
        $responseData = [
            'id' => $message->id,
            'message' => $message->message,
            'file_path' => $message->file_path,
            'timestamp' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
            'status' => $message->status,
            'read' => false,
            'sender_avatar' => $senderAvatar,
        ];
    
        broadcast(new NewMessage([
            'id' => $message->id,
            'from_user_id' => $currentUser->id,
            'to_user_id' => $toUserId,
            'message' => $messageText,
            'file_path' => $message->file_path,
            'timestamp' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
            'status' => $message->status,
            'sender_avatar' => $senderAvatar,
        ]))->toOthers();
    
        $this->updateUserOnlineStatus($currentUser->id);
    
        return response()->json($responseData);
    }
    
    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        $request->validate([
            'from_user_id' => 'required|exists:users,id',
        ]);

        $currentUser = Auth::user();
        $fromUserId = $request->from_user_id;

        // Mark all messages from the specified user as read
        $updatedCount = Message::where('from_user_id', $fromUserId)
            ->where('to_user_id', $currentUser->id)
            ->where('read', false)
            ->update(['read' => true]);

        // Update current user's online status
        $this->updateUserOnlineStatus($currentUser->id);

        return response()->json([
            'success' => true,
            'count' => $updatedCount
        ]);
    }

    /**
     * Send typing indicator
     */
    public function sendTypingIndicator(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
        ]);

        $currentUser = Auth::user();
        $toUserId = $request->to_user_id;

        // Broadcast typing indicator to recipient
        broadcast(new UserTyping([
            'from_user_id' => $currentUser->id,
            'to_user_id' => $toUserId,
            'timestamp' => now()->toDateTimeString(),
        ]))->toOthers();

        // Update current user's online status
        $this->updateUserOnlineStatus($currentUser->id);

        return response()->json(['success' => true]);
    }

    /**
     * Update a user's online status in the cache
     */
    private function updateUserOnlineStatus($userId)
{
    $expiresAt = Carbon::now()->addMinutes(2);
    $wasOffline = !Cache::has('user-online-' . $userId); // Check if the user was previously offline
    Cache::put('user-online-' . $userId, true, $expiresAt);

    // If the user was offline and is now coming online, check for undelivered messages
    if ($wasOffline) {
        $this->checkUndeliveredMessages($userId);
    }
}
    /**
     * Check if a user is online
     */
    private function isUserOnline($userId)
    {
        return Cache::has('user-online-' . $userId);
    }

    /**
     * Manually set user as offline (for logout, etc.)
     */
    public function setOffline()
    {
        $currentUser = Auth::user();
        Cache::forget('user-online-' . $currentUser->id); // Correct the typo

        return response()->json(['success' => true]);
    }

    /**
     * Show a specific chat or conversation
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $currentUser = Auth::user();

        // Get the other user
        $user = User::findOrFail($id);

        // Return a view with both users
        return view('backend.chat.show', [
            'currentUser' => $currentUser,
            'user' => $user
        ]);
    }

    /**
     * Mark a message as delivered
     */
    public function markMessageAsDelivered(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id',
        ]);
    
        $messageId = $request->message_id;
        $message = Message::find($messageId);
        
        if ($message) {
            $message->status = 'delivered';
            $message->save();
    
            // Broadcast to the sender that their message has been delivered
            broadcast(new MessageStatusUpdated($messageId, 'delivered', $message->from_user_id))->toOthers();
    
            return response()->json([
                'success' => true,
                'message_id' => $messageId,
                'status' => 'delivered'
            ]);
        }
    
        return response()->json(['success' => false], 404);
    }

    /**
     * Mark a message as seen
     */
    public function markMessageAsSeen(Request $request)
{
    $request->validate([
        'message_id' => 'required|exists:messages,id',
    ]);

    $currentUser = Auth::user();
    $messageId = $request->message_id;

    $message = Message::find($messageId);
    if ($message) {
        $message->status = 'seen';
        $message->read = true;
        $message->save();

        // Broadcast to the sender that their message has been seen
        broadcast(new MessageStatusUpdated($messageId, 'seen', $message->from_user_id))->toOthers();

        return response()->json([
            'success' => true,
            'message_id' => $messageId,
            'status' => 'seen'
        ]);
    }

    return response()->json(['success' => false], 404);
}
/**
 * Check for undelivered messages and update their status when a user comes online
 */
public function checkUndeliveredMessages($userId)
{
    // Find all messages sent to this user that are still in "sent" status
    $undeliveredMessages = Message::where('to_user_id', $userId)
        ->where('status', 'sent')
        ->get();

    // Update each message to "delivered"
    foreach ($undeliveredMessages as $message) {
        $message->status = 'delivered';
        $message->delivered_at = now();
        $message->save();

        // Broadcast the status update to the sender
        broadcast(new MessageStatusUpdated($message->id, 'delivered', $message->from_user_id))->toOthers();
    }

    return response()->json([
        'success' => true,
        'updated_count' => $undeliveredMessages->count(),
    ]);
}


public function replyToMessage(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'reply_to_message_id' => 'required|exists:messages,id',
        ]);

        $currentUser = Auth::user();
        $toUserId = $request->to_user_id;
        $messageText = $request->message;
        $replyToMessageId = $request->reply_to_message_id;

        $replyMessage = Message::find($replyToMessageId);
        $replyContent = $replyMessage->message;

        $message = new Message();
        $message->from_user_id = $currentUser->id;
        $message->to_user_id = $toUserId;
        $message->message = "Reply to: \"$replyContent\"\n$messageText";
        $message->read = false;
        $message->status = 'sent';
        $message->save();

        // Broadcast the new message
        broadcast(new NewMessage([
            'id' => $message->id,
            'from_user_id' => $currentUser->id,
            'to_user_id' => $toUserId,
            'message' => $message->message,
            'timestamp' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
            'status' => $message->status,
        ]))->toOthers();

        return response()->json([
            'id' => $message->id,
            'message' => $message->message,
            'timestamp' => Carbon::parse($message->created_at)->format('Y-m-d H:i:s'),
            'status' => $message->status,
        ]);
    }

   
}
