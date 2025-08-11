<?php

namespace App\Listeners;

use App\Events\UserMentioned;
use App\Http\Controllers\NotificationController;

class NotifyMentionedUser
{
    protected $notificationController;

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }

    public function handle(UserMentioned $event)
    {
        $this->notificationController->createCommentNotifications($event->comment, [$event->user]);
    }
}