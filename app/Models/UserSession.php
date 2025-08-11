<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'device_fingerprint', 'ip_address',
        'user_agent', 'last_activity', 'risk_score'
    ];

    protected $casts = [
        'last_activity' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function securityEvents()
    {
        return $this->hasMany(SecurityEvent::class, 'session_id', 'session_id');
    }
}