<?php
namespace App\Services;

use Monolog\Logger;

class CommentLogger {
    private $logger;

    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function logRequest(array $data): void {
        $this->logger->info('Comment creation request:', [
            'username' => $data['username'],
            'mentioned_users' => $data['mentioned_users'] ?? [],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}