<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawLeadComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_lead_id',
        'comment',
        'created_by',
        'follow_up_date'
    ];

    protected $casts = [
        'follow_up_date' => 'datetime',
    ];

    // Relationship to RawLead
    public function rawLead()
    {
        return $this->belongsTo(RawLead::class);
    }

    // Relationship to User who created the comment
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}