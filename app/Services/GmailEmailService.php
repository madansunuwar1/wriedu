<?php
namespace App\Services;

use Google_Client;
use Google_Service_Gmail;
use App\Models\GmailAccount;
use App\Models\Email;
use Illuminate\Support\Facades\Log;

class GmailEmailService
{
    public function fetchEmails(GmailAccount $account, $maxResults = 100)
    {
        try {
            // Initialize Google Client
            $client = new Google_Client();
            $client->setAccessToken(json_decode($account->access_token, true));

            // Refresh token if expired
            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                
                // Update stored access token
                $account->update([
                    'access_token' => json_encode($client->getAccessToken())
                ]);
            }

            // Create Gmail Service
            $service = new Google_Service_Gmail($client);
            $user = 'me';

            // Fetch messages
            $results = $service->users_messages->listUsersMessages($user, [
                'maxResults' => $maxResults,
                'labelIds' => ['INBOX'] // Fetch only inbox emails
            ]);

            // Process each message
            $processedEmails = [];
            foreach ($results->getMessages() as $message) {
                $emailDetails = $this->processEmailMessage($service, $user, $message->getId());
                
                // Save or update email in database
                $storedEmail = Email::updateOrCreate(
                    ['message_id' => $emailDetails['message_id']],
                    [
                        'gmail_account_id' => $account->id,
                        'subject' => $emailDetails['subject'],
                        'from' => $emailDetails['from'],
                        'to' => $emailDetails['to'],
                        'body' => $emailDetails['body'],
                        'received_at' => $emailDetails['received_at'],
                        'is_read' => $emailDetails['is_read']
                    ]
                );

                $processedEmails[] = $storedEmail;
            }

            return $processedEmails;

        } catch (\Exception $e) {
            // Log error
            Log::error('Gmail Email Fetch Error: ' . $e->getMessage());
            
            return false;
        }
    }

    private function processEmailMessage($service, $user, $messageId)
    {
        // Fetch full message details
        $message = $service->users_messages->get($user, $messageId);
        
        // Extract headers
        $headers = $message->getPayload()->getHeaders();
        
        // Extract email metadata
        $subject = $this->findHeaderValue($headers, 'Subject');
        $from = $this->findHeaderValue($headers, 'From');
        $to = $this->findHeaderValue($headers, 'To');
        $date = $this->findHeaderValue($headers, 'Date');

        // Determine if email is read
        $isRead = !in_array('UNREAD', $message->getLabelIds());

        // Extract email body
        $body = $this->getEmailBody($message);

        return [
            'message_id' => $messageId,
            'subject' => $subject,
            'from' => $from,
            'to' => $to,
            'body' => $body,
            'received_at' => \Carbon\Carbon::parse($date),
            'is_read' => $isRead
        ];
    }

    private function findHeaderValue($headers, $name)
    {
        foreach ($headers as $header) {
            if ($header->getName() === $name) {
                return $header->getValue();
            }
        }
        return null;
    }

    private function getEmailBody($message)
    {
        $payload = $message->getPayload();
        
        // Handle different MIME types
        if ($payload->getMimeType() === 'multipart/alternative') {
            // Find plain text or HTML part
            foreach ($payload->getParts() as $part) {
                if (in_array($part->getMimeType(), ['text/plain', 'text/html'])) {
                    return base64_decode(strtr($part->getBody()->getData(), '-_', '+/'));
                }
            }
        } elseif (in_array($payload->getMimeType(), ['text/plain', 'text/html'])) {
            // Direct body
            return base64_decode(strtr($payload->getBody()->getData(), '-_', '+/'));
        }

        return 'Unable to extract email body';
    }
}