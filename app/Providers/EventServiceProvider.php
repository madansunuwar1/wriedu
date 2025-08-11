<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

// Import our custom events and listeners
use App\Events\CommentCreated;
use App\Events\NoticeCreated;
use App\Events\UserMentioned;
use App\Listeners\SendNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Custom notification events
        CommentCreated::class => [
            SendNotification::class,
        ],
        NoticeCreated::class => [
            SendNotification::class,
        ],
        UserMentioned::class => [
            SendNotification::class,
        ],
        // Add other events here as needed
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}