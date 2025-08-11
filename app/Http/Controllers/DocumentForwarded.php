<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentForwarded extends Mailable
{
    use Queueable, SerializesModels;

    public $lead;
    public $sender;
    public $notes;

    /**
     * Create a new message instance.
     *
     * @param mixed $lead
     * @param mixed $sender
     * @param string $notes
     */
    public function __construct($lead, $sender, $notes)
    {
        $this->lead = $lead;
        $this->sender = $sender;
        $this->notes = $notes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Document Forwarded')
                    ->view('emails.document_forwarded'); // Ensure this path is correct
    }
}