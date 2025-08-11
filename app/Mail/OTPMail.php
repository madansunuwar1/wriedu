<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $otp;
    public $userName;
    public $greeting;
    public $introLines;
    public $outroLines;
    public $salutation;
    public $actionText;
    
    /**
     * Create a new message instance
     */
    public function __construct(
        string $otp,
        string $userName = '',
        ?string $greeting = null,
        array $introLines = [],
        array $outroLines = [],
        ?string $salutation = null,
        ?string $actionText = null
    ) {
        $this->otp = str_pad($otp, 6, '0', STR_PAD_LEFT);
        $this->userName = $userName;
        $this->greeting = $greeting;
        $this->introLines = $introLines;
        $this->outroLines = $outroLines;
        $this->salutation = $salutation;
        $this->actionText = $actionText;
    }
    
    /**
     * Build the message
     */
    public function build()
    {
        return $this->markdown('emails.otp')
                    ->subject('Verify Your OTP');
    }
}