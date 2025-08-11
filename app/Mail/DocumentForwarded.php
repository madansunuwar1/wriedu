<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Lead;
use App\Models\User;

class DocumentForwarded extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $lead;
    public $sender;
    public $recipient;
    public $notes;
    public $applicationId;
    public $applicantName; // Add this property

    /**
     * Create a new message instance.
     *
     * @param Lead $lead
     * @param User $sender
     * @param User $recipient
     * @param string|null $notes
     * @param int $applicationId
     * @return void
     */
    public function __construct(Lead $lead, User $sender, User $recipient, $notes = null, $applicationId)
    {
        $this->lead = $lead;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->notes = $notes;
        $this->applicationId = $applicationId;
        $this->applicantName = $lead->name; // Use the lead's name as the applicant's name
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        \Log::info('Building DocumentForwarded email', [
            'lead_id' => $this->lead->id,
            'sender' => $this->sender->name,
            'recipient' => $this->recipient->name,
            'recipient_email' => $this->recipient->email,
            'notes' => $this->notes ?? 'No notes provided',
            'application_id' => $this->applicationId,
            'applicant_name' => $this->applicantName,
        ]);
        
        // Use the applicant's name in the subject
        return $this->subject('Document Forwarded: ' . $this->applicantName)
                    ->markdown('emails.document_forwarded', [
                        'applicationId' => $this->applicationId,
                        'applicantName' => $this->applicantName, // Pass the applicant's name to the view
                    ]);
    }
}