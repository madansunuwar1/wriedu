<?php

// App/Models/Otp.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'expires_at'
    ];

    protected $dates = [
        'expires_at',
        'created_at',
        'updated_at'
    ];

    public function isExpired(): bool
    {
        return Carbon::now()->isAfter($this->expires_at);
    }
}