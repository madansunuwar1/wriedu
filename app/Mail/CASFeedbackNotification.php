<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CASFeedbackNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $casFeedback;
    public $application;
    public $recipientType; // 'student' or 'partner'

    public function __construct($casFeedback, $application, $recipientType = 'student')
    {
        $this->casFeedback = $casFeedback;
        $this->application = $application;
        $this->recipientType = $recipientType;
    }

    public function build()
    {
        $subject = "New CAS Feedback: " . $this->casFeedback->subject;
        
        return $this->subject($subject)
                    ->view('emails.cas-feedback-notification')
                    ->with([
                        'casFeedback' => $this->casFeedback,
                        'application' => $this->application,
                        'recipientType' => $this->recipientType,
                    ]);
    }
}