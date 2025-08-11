<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class LeadComment extends Model
{
    use HasFactory;

    protected $fillable = [
          
        'comment',
        'user_id',
        'lead_id',
        'comment_type',
        'application_id',
        'enquiry_id',
        'updated_by',
        'editor_name',
        'lead_id',
        'date_time',
    ];


    public function editor() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($comment) {
            if (auth()->check()) {
                $comment->editor_name = auth()->user()->name;
            }
        });
    }

    public function mentions()
    {
        return $this->hasMany(CommentMention::class, 'comment_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
    
public function createdBy()
{
    return $this->belongsTo(User::class, 'user_id');
}
    
    public function application()
{
    return $this->belongsTo(Application::class);
}

public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}


}
