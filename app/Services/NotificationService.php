<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a single notification
     */
    public function create(array $data)
    {
        // Ensure all required fields are present
        $requiredFields = ['user_id', 'message', 'type'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        // Create the notification with proper data handling
        return Notification::create([
            'user_id' => $data['user_id'],
            'message' => $data['message'],
            'content' => $data['content'] ?? null,
            'link' => $data['link'] ?? null,
            'read' => false,
            'type' => $data['type'],
            'data' => is_array($data['data']) ? json_encode($data['data']) : $data['data']
        ]);
    }

    /**
     * Create notifications for multiple users
     */
    public function notifyUsers(array $userIds, array $notificationData)
    {
        $notifications = [];
        
        try {
            DB::beginTransaction();
            
            foreach ($userIds as $userId) {
                $data = array_merge(['user_id' => $userId], $notificationData);
                $notifications[] = $this->create($data);
            }
            
            DB::commit();
            return $notifications;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create notifications: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Notify all users except the specified ones
     */
    public function notifyAllExcept(array $excludeUserIds, array $notificationData)
    {
        $users = User::whereNotIn('id', $excludeUserIds)->get();
        return $this->notifyUsers($users->pluck('id')->toArray(), $notificationData);
    }
}