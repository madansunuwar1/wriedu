<?php
// app/Events/CommentCreated.php
namespace App\Events;

use App\Models\LeadComment;

class CommentCreated extends NotifiableEvent
{
    protected $comment;
    protected $mentionedUserIds;

    public function __construct(LeadComment $comment, array $mentionedUserIds = [])
    {
        $this->comment = $comment;
        $this->mentionedUserIds = $mentionedUserIds;
    }

    public function getNotificationData(): array
    {
        return [
            'message' => auth()->user()->name . ' added a new comment',
            'content' => \Illuminate\Support\Str::limit($this->comment->comment, 100),
            'link' => '/leadcomment/' . $this->comment->id,
            'type' => 'comment',
            'data' => [
                'comment_id' => $this->comment->id,
                'author_id' => auth()->id(),
                'notification_type' => 'general'
            ]
        ];
    }

    public function getUserIds(): array
    {
        // Notify all except current user and mentioned users
        $excludeIds = array_merge([$this->comment->user_id], $this->mentionedUserIds);
        return \App\Models\User::whereNotIn('id', $excludeIds)->pluck('id')->toArray();
    }
}