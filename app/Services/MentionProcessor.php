<?php
namespace App\Services;

use App\Models\User;
use App\Services\NotificationService;

class MentionProcessor {
    private $notificationService;

    public function __construct(NotificationService $notificationService) {
        $this->notificationService = $notificationService;
    }

    public function process(array $mentionedUserIds): void {
        foreach ($mentionedUserIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $this->notificationService->sendMentionNotification($user);
            }
        }
    }
}