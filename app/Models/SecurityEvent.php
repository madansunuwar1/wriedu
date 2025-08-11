<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'event_type', 'details',
        'ip_address', 'user_agent', 'timestamp'
    ];

    protected $casts = [
        'details' => 'array',
        'timestamp' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}