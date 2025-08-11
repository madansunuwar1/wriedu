<?php
namespace App\Events;

use App\Models\User;
use App\Models\LeadComment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserMentioned implements ShouldBroadcast 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $comment;

    public function __construct(User $user, LeadComment $comment)
    {
        $this->user = $user;
        $this->comment = $comment;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->user->id);
    }

    public function broadcastAs()
    {
        return 'user.mentioned';
    }

    public function broadcastWith()
    {
        return [
            'comment_id' => $this->comment->id,
            'mentioner_name' => $this->comment->author_name,
            'mentioned_name' => $this->user->name,  // Added mentioned user's name
            'comment_text' => $this->comment->comment,
            'time' => now()->toISOString()
        ];
    }
}