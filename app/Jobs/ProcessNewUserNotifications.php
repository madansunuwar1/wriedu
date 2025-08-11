<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewUserRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class ProcessNewUserNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $newUser;
    public $timeout = 2;
    public $tries = 1;

    public function __construct(User $user)
    {
        $this->newUser = $user;
        $this->onQueue('emails-high');
    }

    public function handle(): void
    {
        $admins = User::query()
            ->select('id', 'name', 'email')
            ->where('is_admin', true)
            ->where('active', true)
            ->where('id', '!=', $this->newUser->id)
            ->limit(10)
            ->get();

        // Process in small chunks to prevent timeouts
        foreach ($admins->chunk(3) as $chunk) {
            Notification::send($chunk, new NewUserRegistration($this->newUser));
        }
    }
}