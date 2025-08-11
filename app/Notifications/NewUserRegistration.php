<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class NewUserRegistration extends Notification implements ShouldQueue
{
    use Queueable;

    protected $newUser;
    protected $otp;
    
    public $timeout = 2;
    public $tries = 1;
    public $afterCommit = true;

    public function __construct(User $user, $otp = null)
    {
        $this->newUser = $user;
        $this->otp = $otp;
        $this->onQueue('emails-high');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->priority(1);

        if ($this->otp) {
            // Store OTP in cache before sending email
            $this->generateOtp($this->newUser->id, $this->otp);
            
            // OTP Verification Email
            return $message
                ->subject('Verify Your Email Address - WRIEducations')
                ->greeting('Hello ' . $this->newUser->name . '!')
                ->line('Thanks for signing up with WRIEducations!')
                ->line('Your verification code is:')
                ->line ('{{ $otp }}')
                ->line('This code will expire in 10 minutes.')
                ->line('If you did not create an account, no further action is required.');
        }

        // New User Registration Notification
        return $message
            ->subject('New User Registration')
            ->greeting('Hello ' . $notifiable->name)
            ->line('A new user has registered on the platform.')
            ->line('New User Details:')
            ->line('Name: ' . $this->newUser->name . ' ' . $this->newUser->last)
            ->line('Email: ' . $this->newUser->email)
            ->line('Applications: ' . $this->newUser->application)
            ->action('View User Profile', url('/users/' . $this->newUser->id))
            ->line('Thank you for using WRIEducations!');
    }
}