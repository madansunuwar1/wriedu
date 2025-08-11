<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MentionNotification extends Notification
{
    use Queueable;

    protected $comment;
    protected $mentionerName;

    public function __construct($comment, $mentionerName)
    {
        $this->comment = $comment;
        $this->mentionerName = $mentionerName;
    }

    public function via($notifiable): array
{
    return ['database'];
}

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'mention',
            'message' => "{$this->mentionerName} mentioned you in a comment",
            'content' => $this->comment->comment,
            'mentioner_name' => $this->mentionerName,
            'created_at' => now()->toDateTimeString(),
            'comment_id' => $this->comment->id
        ];
    }
}