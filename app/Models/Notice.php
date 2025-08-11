<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'image',
        'read_status',
        'user_id',
        'read_at',
        'display_start_at',
        'display_end_at',
    ];

    protected $casts = [
        'read_status' => 'boolean',
        'read_at' => 'datetime',
        'display_start_at' => 'datetime',
        'display_end_at' => 'datetime',
    ];

    // Define relationship with users
    public function users()
    {
        return $this->belongsToMany(User::class, 'notice_user')
                    ->withPivot('seen_at')
                    ->withTimestamps();
    }

    public function seenByUsers()
    {
        return $this->belongsToMany(User::class, 'notice_user')
                    ->withPivot('seen')
                    ->wherePivot('seen', true);
    }
}