<?php
namespace App\Handlers;

use Exception;
use App\Services\RateLimiter;
use App\Services\CommentLogger;
use App\Services\MentionProcessor;

class CommentRequestHandler {
    private $rateLimiter;
    private $logger;
    private $mentionProcessor;

    public function __construct(
        RateLimiter $rateLimiter,
        CommentLogger $logger,
        MentionProcessor $mentionProcessor
    ) {
        $this->rateLimiter = $rateLimiter;
        $this->logger = $logger;
        $this->mentionProcessor = $mentionProcessor;
    }

    public function validateRequest(array $requestData): array {
        $errors = [];
        $sanitizedData = [];
        
        // Extract request_all data
        $requestAll = $requestData['request_all'] ?? [];
        
        // Validate CSRF token
        if (empty($requestAll['_token'])) {
            $errors[] = 'Missing CSRF token';
        }
        
        // Validate username
        if (empty($requestAll['uname'])) {
            $errors[] = 'Username is required';
        } else {
            $sanitizedData['username'] = strip_tags($requestAll['uname']);
        }
        
        // Validate comment content
        if (empty($requestAll['comment'])) {
            $errors[] = 'Comment content is required';
        } else {
            $sanitizedData['comment'] = strip_tags($requestAll['comment']);
        }
        
        // Parse mentioned users - use the root level mentioned_users
        try {
            // Get mentioned_users from root level of request
            $mentionedUsers = $requestData['mentioned_users'] ?? '';
            
            // Parse the string into an array
            if (is_string($mentionedUsers)) {
                // Remove any quotes and brackets
                $mentionedUsers = trim($mentionedUsers, '"[]');
                // Split by comma if multiple values
                $userIds = explode(',', $mentionedUsers);
                // Convert to integers and filter empty values
                $userIds = array_filter(array_map('intval', $userIds));
                
                if (!empty($userIds)) {
                    $sanitizedData['mentioned_users'] = array_values($userIds);
                }
            }
        } catch (Exception $e) {
            $errors[] = 'Invalid mentioned_users format';
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors,
            'sanitizedData' => $sanitizedData
        ];
    }

    public function processComment(array $sanitizedData): bool {
        try {
            if ($this->rateLimiter->isLimited($sanitizedData['username'])) {
                throw new Exception('Too many comments in short time period');
            }
            
            // Log the request before processing
            $this->logger->logRequest($sanitizedData);
            
            // Process mentions if present
            if (!empty($sanitizedData['mentioned_users'])) {
                // Ensure mentioned_users is an array before processing
                $mentionedUsers = (array)$sanitizedData['mentioned_users'];
                $this->mentionProcessor->process($mentionedUsers);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Comment creation failed: " . $e->getMessage());
            return false;
        }
    }
}