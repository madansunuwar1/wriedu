<?php

use Illuminate\Support\Str;

return [
    'rate_limit' => [
        'max_attempts' => env('COMMENT_RATE_LIMIT_MAX_ATTEMPTS', 5),
        'decay_minutes' => env('COMMENT_RATE_LIMIT_DECAY_MINUTES', 1),
    ],
];