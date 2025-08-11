<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Oauth2;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use App\Models\GmailAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleOAuthController extends Controller
{
    public function handleGoogleCallback()
    {
        try {
            // Create Google Client with proper configuration
            $client = new Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect'));
            
            // Set scopes for Gmail access
            $client->setScopes([
                Google_Service_Gmail::GMAIL_READONLY,
                'https://www.googleapis.com/auth/userinfo.email'
            ]);

            // Validate the authentication code
            $authCode = request('code');
            if (empty($authCode)) {
                throw new \Exception('Authentication code is missing');
            }

            // Fetch access token
            $token = $client->fetchAccessTokenWithAuthCode($authCode);

            // Check for errors in token retrieval
            if (isset($token['error'])) {
                Log::error('Google OAuth Token Error: ' . $token['error']);
                return redirect()->route('gmail.connect')
                    ->with('error', 'Failed to authenticate with Google. Please try again.');
            }

            // Set the access token
            $client->setAccessToken($token);

            // Verify token and get user information
            $oauth2 = new Google_Service_Oauth2($client);
            $userInfo = $oauth2->userinfo->get();
            $email = $userInfo->email;

            // Validate user email
            if (!$email) {
                throw new \Exception('Could not retrieve email from Google');
            }

            // Check if this email is already connected for this user
            $existingAccount = GmailAccount::where('email', $email)
                ->where('user_id', Auth::id())
                ->first();

            if ($existingAccount) {
                return redirect()->route('gmail.connect')
                    ->with('error', 'This Gmail account is already connected');
            }

            // Prepare token data for storage
            $accessToken = $token;
            $refreshToken = $token['refresh_token'] ?? null;

            // Create new Gmail account connection
            $gmailAccount = GmailAccount::create([
                'user_id' => Auth::id(),
                'email' => $email,
                'access_token' => json_encode($accessToken),
                'refresh_token' => $refreshToken,
                'expires_at' => now()->addSeconds($token['expires_in'] ?? 3600)
            ]);

            // Optional: Log the successful connection
            Log::info('Gmail account connected', [
                'user_id' => Auth::id(),
                'email' => $email
            ]);

            return redirect()->route('gmail.connect')
                ->with('success', 'Gmail account connected successfully');

        } catch (\Exception $e) {
            // Log the full error for debugging
            Log::error('Google OAuth Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('gmail.connect')
                ->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    // Method to refresh access token
    public function refreshToken(GmailAccount $gmailAccount)
    {
        try {
            $client = new Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));

            // Set the current access token
            $currentToken = json_decode($gmailAccount->access_token, true);
            $client->setAccessToken($currentToken);

            // Refresh the token if it's expired
            if ($client->isAccessTokenExpired()) {
                $refreshToken = $gmailAccount->refresh_token;
                
                if ($refreshToken) {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                    
                    // Update the stored token
                    $gmailAccount->update([
                        'access_token' => json_encode($newToken),
                        'expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600)
                    ]);

                    return $newToken;
                } else {
                    throw new \Exception('No refresh token available');
                }
            }

            return $currentToken;
        } catch (\Exception $e) {
            Log::error('Token Refresh Error', [
                'message' => $e->getMessage(),
                'account_id' => $gmailAccount->id
            ]);

            return null;
        }
    }
    public function redirectToGoogle()
{
    try {
        // Detailed configuration logging
        Log::info('Google OAuth Configuration', [
            'client_id' => config('services.google.client_id') ? 'Set' : 'Not Set',
            'client_secret' => config('services.google.client_secret') ? 'Set' : 'Not Set',
            'redirect_uri' => config('services.google.redirect'),
        ]);

        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        
        // Comprehensive scopes
        $client->setScopes([
            Google_Service_Gmail::GMAIL_READONLY,
            'https://www.googleapis.com/auth/userinfo.email'
        ]);

        // Additional OAuth configuration
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        // Generate authorization URL
        $authUrl = $client->createAuthUrl();

        Log::info('Generated OAuth Authorization URL', [
            'url' => $authUrl
        ]);

        return redirect()->away($authUrl);
    } catch (\Exception $e) {
        // Comprehensive error logging
        Log::error('Google OAuth Redirect Failed', [
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect')
        ]);
        
        return redirect()->route('gmail.connect')
            ->with('error', 'OAuth Redirect Failed: ' . $e->getMessage());
    }
}


public function listEmails()
{
    // Get the first connected Gmail account
    $gmailAccount = GmailAccount::where('user_id', Auth::id())->first();

    if (!$gmailAccount) {
        return redirect()->route('gmail.connect')
            ->with('error', 'Please connect a Gmail account first');
    }

    $emails = $this->fetchEmails($gmailAccount);

    return view('backend.gmail.index', compact('emails'));
}

public function showEmail($id)
{
    try {
        // Add more detailed logging
        Log::info('Attempting to show email', [
            'email_id' => $id,
            'user_id' => Auth::id()
        ]);

        // Get the connected Gmail account
        $gmailAccount = GmailAccount::where('user_id', Auth::id())->first();

        if (!$gmailAccount) {
            Log::error('No Gmail account connected for user');
            return redirect()->route('gmail.connect')
                ->with('error', 'No Gmail account connected');
        }

        // Refresh token if expired
        $tokenResult = $this->refreshToken($gmailAccount);
        if (!$tokenResult) {
            Log::error('Failed to refresh token');
            return redirect()->route('gmail.connect')
                ->with('error', 'Failed to refresh authentication token');
        }

        // Reload the account to get updated token
        $gmailAccount->refresh();

        // Set up Google Client
        $client = new Google_Client();
        $client->setAccessToken(json_decode($gmailAccount->access_token, true));

        // Validate client access token
        if ($client->isAccessTokenExpired()) {
            Log::error('Access token is expired even after refresh');
            return redirect()->route('gmail.connect')
                ->with('error', 'Authentication expired. Please reconnect your Gmail account.');
        }

        // Create Gmail Service
        $service = new Google_Service_Gmail($client);
        
        // Add error handling for message retrieval
        try {
            // Fetch full message details
            $message = $service->users_messages->get('me', $id);
        } catch (\Google_Service_Exception $e) {
            Log::error('Google Service Error', [
                'message' => $e->getMessage(),
                'errors' => $e->getErrors()
            ]);
            return redirect()->route('emails.index')
                ->with('error', 'Failed to retrieve email: ' . $e->getMessage());
        }
        
        // Get message payload
        $payload = $message->getPayload();
        
        // Extract headers
        $headers = $payload->getHeaders();
        
        // Function to get header value
        $getHeaderValue = function($headers, $name) {
            foreach ($headers as $header) {
                if (strtolower($header->getName()) == strtolower($name)) {
                    return $header->getValue();
                }
            }
            return 'N/A';
        };

        // Enhanced body extraction
        $body = '';
        if ($payload->getBody() && $payload->getBody()->getData()) {
            $body = base64_decode(strtr($payload->getBody()->getData(), '-_', '+/'));
        } elseif ($payload->getParts()) {
            foreach ($payload->getParts() as $part) {
                // Try to get HTML or plain text body
                if ($part->getMimeType() == 'text/html' || $part->getMimeType() == 'text/plain') {
                    $body = base64_decode(strtr($part->getBody()->getData(), '-_', '+/'));
                    break;
                }
            }
        }

        // Prepare email data
        $email = [
            'id' => $id,
            'subject' => $getHeaderValue($headers, 'Subject'),
            'from' => $getHeaderValue($headers, 'From'),
            'to' => $getHeaderValue($headers, 'To'),
            'date' => $getHeaderValue($headers, 'Date'),
            'body' => $body
        ];

        // Debug logging
        Log::info('Email Fetched Successfully', [
            'email_id' => $id,
            'subject' => $email['subject']
        ]);

        return view('backend.gmail.show', compact('email'));

    } catch (\Exception $e) {
        // Comprehensive error logging
        Log::error('Email Fetch Error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'email_id' => $id
        ]);

        return redirect()->route('emails.index')
            ->with('error', 'An unexpected error occurred: ' . $e->getMessage());
    }
}

// Helper methods to extract headers and body
private function getHeader($headers, $name)
{
    foreach ($headers as $header) {
        if ($header->getName() == $name) {
            return $header->getValue();
        }
    }
    return null;
}

private function getBody($payload)
{
    $body = '';
    if ($payload->getBody()->getData()) {
        $body = base64_decode(strtr($payload->getBody()->getData(), '-_', '+/'));
    } elseif ($payload->getParts()) {
        foreach ($payload->getParts() as $part) {
            if ($part->getMimeType() == 'text/plain') {
                $body = base64_decode(strtr($part->getBody()->getData(), '-_', '+/'));
                break;
            }
        }
    }
    return $body;
}

public function sendEmail(GmailAccount $gmailAccount, $to, $subject, $body)
{
    try {
        $client = new Google_Client();
        $client->setAccessToken(json_decode($gmailAccount->access_token, true));

        $service = new Google_Service_Gmail($client);

        // Create the email message
        $message = new Google_Service_Gmail_Message();
        $rawMessageString = strtr(base64_encode(
            "To: $to\r\n" .
            "Subject: $subject\r\n\r\n" .
            "$body"
        ), array('+' => '-', '/' => '_'));
        $message->setRaw($rawMessageString);

        // Send the message
        $service->users_messages->send('me', $message);

        return true;
    } catch (\Exception $e) {
        Log::error('Gmail Send Error', [
            'message' => $e->getMessage(),
            'account_id' => $gmailAccount->id
        ]);

        return false;
    }
}
public function fetchEmails(GmailAccount $gmailAccount)
{
    try {
        // Create Google Client
        $client = new Google_Client();
        $client->setAccessToken(json_decode($gmailAccount->access_token, true));

        // Create Gmail Service
        $service = new Google_Service_Gmail($client);

        // Fetch emails (example: retrieve latest 10 emails)
        $results = $service->users_messages->listUsersMessages('me', [
            'maxResults' => 10,
            'labelIds' => ['INBOX']
        ]);

        $emails = [];
        foreach ($results->getMessages() as $message) {
            // Fetch full message details
            $fullMessage = $service->users_messages->get('me', $message->getId());
            
            // Extract headers
            $headers = $fullMessage->getPayload()->getHeaders();
            $subject = '';
            $from = '';
            
            foreach ($headers as $header) {
                if ($header->getName() == 'Subject') {
                    $subject = $header->getValue();
                }
                if ($header->getName() == 'From') {
                    $from = $header->getValue();
                }
            }

            $emails[] = [
                'id' => $message->getId(),
                'subject' => $subject,
                'from' => $from,
                // Add more details as needed
            ];
        }

        return $emails;

    } catch (\Exception $e) {
        // Log error and handle potential token expiration
        Log::error('Gmail Fetch Error', [
            'message' => $e->getMessage(),
            'account_id' => $gmailAccount->id
        ]);

        return null;
    }
}
public function showGmailConnectPage()
{
    // Retrieve any existing connected Gmail accounts for the current user
    $connectedAccounts = GmailAccount::where('user_id', Auth::id())->get();

    return view('backend.gmail.connect', [
        'connectedAccounts' => $connectedAccounts
    ]);
}
}