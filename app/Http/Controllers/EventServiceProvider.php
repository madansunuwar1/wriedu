<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\UserMentioned;
use App\Listeners\NotifyMentionedUser;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserMentioned::class => [
            NotifyMentionedUser::class,
        ],
    ];

    public function boot()
    {
        parent::boot();

        // Bind NotificationController to the container
        $this->app->singleton(NotificationController::class, function ($app) {
            return new NotificationController();
        });
    }
}