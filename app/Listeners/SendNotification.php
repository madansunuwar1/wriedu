<?php
// app/Listeners/SendNotification.php
namespace App\Listeners;

use App\Events\NotifiableEvent;
use App\Services\NotificationService;

class SendNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(NotifiableEvent $event)
    {
        $userIds = $event->getUserIds();
        $notificationData = $event->getNotificationData();
        
        $this->notificationService->notifyUsers($userIds, $notificationData);
    }
}