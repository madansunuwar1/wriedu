<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'name',
        'email',
        'phone',
        'location',
        'previous_degree',
        'twelfth_english_grade',
        'pass_degree_gpa',
        'pass_year',
        'english_proficiency',
        'english_score',
        'preferred_country',
        'preferred_subject',
        'applying_for',
        'source',          // Added to match migration
        'status',
        'created_by',
        'assigned_to',
        'follow_up_comments',
    ];

    protected $casts = [
        'pass_year' => 'integer', // Cast pass_year as integer since it's a year field
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(RawLeadComment::class);
    }
}