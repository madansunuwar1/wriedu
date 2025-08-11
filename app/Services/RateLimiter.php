<?php
namespace App\Services;

use Redis;

class RateLimiter {
    private $redis;
    private $maxAttempts;
    private $decayMinutes;

    public function __construct(Redis $redis, int $maxAttempts = 5, int $decayMinutes = 1) {
        $this->redis = $redis;
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }

    public function isLimited(string $username): bool {
        $key = "comment_limit:{$username}";
        $attempts = $this->redis->get($key) ?: 0;

        if ($attempts >= $this->maxAttempts) {
            return true;
        }

        $this->redis->incr($key);
        $this->redis->expire($key, $this->decayMinutes * 60);

        return false;
    }
}