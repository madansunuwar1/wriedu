<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message_id;
    public $status;
    protected $to_user_id; // The ID of the user who needs to receive this update.

    /**
     * Create a new event instance.
     *
     * @param int $messageId
     * @param string $status
     * @param int $toUserId  <-- Renamed for clarity
     */
    public function __construct($messageId, $status, $toUserId)
    {
        $this->message_id = $messageId;
        $this->status = $status;
        $this->to_user_id = $toUserId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        // Broadcast to the user who needs the update (the original sender).
        return new PrivateChannel('chat.' . $this->to_user_id);
    }

    /**
     * Note on broadcastWith():
     * You had a broadcastWith() method. You do NOT need this if you just want
     * to send all the public properties of the class. Laravel does this automatically.
     * By making `$message_id` and `$status` public, they will be sent by default.
     * This simplifies the code.
     */
}