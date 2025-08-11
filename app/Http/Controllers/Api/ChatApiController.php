<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageStatusUpdated;
use App\Events\NewMessage;
use App\Events\UserTyping;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ChatApiController extends Controller
{
    /**
     * Get the currently authenticated user's details.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentUser()
    {
        $user = Auth::user();
        if ($user) {
            $user->avatar_url = $user->avatar
                ? asset('storage/avatars/' . basename($user->avatar))
                : asset('assets/images/profile/user-1.jpg');
        }
        return response()->json($user);
    }

    /**
     * Get a list of users, their last message, unread count, and online status.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers()
    {
        $currentUser = Auth::user();
        $this->updateUserOnlineStatus($currentUser->id, true);

        $users = User::where('id', '!=', $currentUser->id)
            ->select('id', 'name', 'last', 'avatar')
            ->get();

        $formattedUsers = $users->map(function($user) use ($currentUser) {
            $unreadCount = Message::where('from_user_id', $user->id)
                ->where('to_user_id', $currentUser->id)
                ->where('read', false)
                ->count();

            $latestMessage = Message::where(function($query) use ($currentUser, $user) {
                $query->where('from_user_id', $currentUser->id)->where('to_user_id', $user->id);
            })->orWhere(function($query) use ($currentUser, $user) {
                $query->where('from_user_id', $user->id)->where('to_user_id', $currentUser->id);
            })->latest()->first();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'last' => $user->last,
                'avatar' => $user->avatar ? asset('storage/avatars/' . basename($user->avatar)) : asset('assets/images/profile/user-1.jpg'),
                'unread' => $unreadCount,
                'lastMessage' => $latestMessage ? ($latestMessage->file_path ? '[File]' : $latestMessage->message) : null,
                'lastMessageTime' => $latestMessage ? $latestMessage->created_at->toIso8601String() : null,
                'online' => Cache::has('user-online-' . $user->id),
            ];
        })->sortByDesc('lastMessageTime')->values();

        return response()->json($formattedUsers);
    }

    /**
     * Get the full message history with a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConversation($userId)
    {
        $currentUser = Auth::user();
        $this->updateUserOnlineStatus($currentUser->id);

        $messages = Message::where(function($query) use ($currentUser, $userId) {
            $query->where('from_user_id', $currentUser->id)->where('to_user_id', $userId);
        })->orWhere(function($query) use ($currentUser, $userId) {
            $query->where('from_user_id', $userId)->where('to_user_id', $currentUser->id);
        })->orderBy('created_at', 'asc')->get();

        // When a conversation is opened, mark all incoming messages as read.
        Message::where('from_user_id', $userId)
            ->where('to_user_id', $currentUser->id)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json($messages);
    }

    /**
     * Store a new message and broadcast it.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:5000',
            'file' => 'nullable|file|mimes:png,jpg,jpeg,gif,webp,xlsx,xls,docx,doc,pdf,zip|max:10240',
        ]);

        $currentUser = Auth::user();

        $message = new Message();
        $message->from_user_id = $currentUser->id;
        $message->to_user_id = $request->to_user_id;
        $message->message = $request->message;
        $message->status = 'sent';
        $message->read = false;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('chat_files', 'public');
            $message->file_path = $filePath;
        }

        $message->save();

        // Broadcast the event with the complete Message model object.
        broadcast(new NewMessage($message))->toOthers();

        // Return the newly created message so the sender's UI can update.
        return response()->json($message, 201);
    }

    /**
     * Broadcast a typing indicator to a user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTypingIndicator(Request $request)
    {
        $request->validate(['to_user_id' => 'required|exists:users,id']);

        broadcast(new UserTyping([
            'from_user_id' => Auth::id(),
            'to_user_id' => $request->to_user_id,
        ]))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Mark incoming messages from a user as "seen" and broadcast the update.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markMessagesAsSeen(Request $request)
    {
        $request->validate(['from_user_id' => 'required|exists:users,id']);

        $messagesToUpdate = Message::where('from_user_id', $request->from_user_id)
            ->where('to_user_id', Auth::id())
            ->where('status', '!=', 'seen')
            ->get();

        foreach ($messagesToUpdate as $message) {
            $message->status = 'seen';
            $message->read = true;
            $message->save();

            // Broadcast the status update back to the original sender.
            broadcast(new MessageStatusUpdated($message->id, 'seen', $message->from_user_id))->toOthers();
        }

        return response()->json(['success' => true, 'count' => $messagesToUpdate->count()]);
    }
    
    /**
     * Manually set the current user as offline by removing them from the cache.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setOffline()
    {
        if (Auth::check()) {
            Cache::forget('user-online-' . Auth::id());
        }
        return response()->json(['success' => true]);
    }

    /**
     * Update a user's online status in the cache.
     *
     * @param int $userId
     * @param bool $checkUndelivered
     */
    private function updateUserOnlineStatus($userId, $checkUndelivered = false)
    {
        $wasOffline = !Cache::has('user-online-' . $userId);
        Cache::put('user-online-' . $userId, true, Carbon::now()->addMinutes(2));

        if ($checkUndelivered && $wasOffline) {
            $this->checkUndeliveredMessages($userId);
        }
    }

    /**
     * Find undelivered messages for a user who just came online and mark them as "delivered".
     *
     * @param int $userId
     */
    private function checkUndeliveredMessages($userId)
    {
        $undeliveredMessages = Message::where('to_user_id', $userId)
            ->where('status', 'sent')
            ->get();

        foreach ($undeliveredMessages as $message) {
            $message->status = 'delivered';
            $message->delivered_at = now();
            $message->save();

            // Broadcast the "delivered" status update back to the sender.
            broadcast(new MessageStatusUpdated($message->id, 'delivered', $message->from_user_id))->toOthers();
        }
    }
}