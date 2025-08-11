<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewMessage extends Notification
{
    use Queueable;

    // Properly declare the property
    protected $userId;
    protected $title;
    protected $message;
    protected $time;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $time, $userId = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->time = $time;
        $this->userId = $userId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'time' => $this->time,
            'user_id' => $this->userId ?? $notifiable->id,
        ];
    }
}