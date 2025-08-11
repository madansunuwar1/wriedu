<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'wantToStudy', 'location', 
        'courselevel', 'lastqualifications', 'passed', 'englishTest',
        'forwarded_to', 'forwarded_notes', 'is_forwarded', 'forwarded_at', 'created_by', 'sources',
    ];

    protected $dates = [
        'forwarded_at'
    ];

    

    // Relationship with User (if forwarded_to is a user ID)
    public function forwardedToUser()
    {
        return $this->belongsTo(User::class, 'forwarded_to');
    }
}