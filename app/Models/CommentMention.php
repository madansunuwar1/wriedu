<?php
// CommentMention Model
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentMention extends Model
{
    protected $fillable = [
        'comment_id',
        'mentioned_user_id',
        'user_name',
        'mentioner_id'  // Add this to fillable array
    ];

    public function comment()
    {
        return $this->belongsTo(LeadComment::class, 'comment_id', 'id');
    }

    public function mentionedUser()
    {
        return $this->belongsTo(User::class, 'mentioned_user_id');
    }

    public function mentioner()
    {
        return $this->belongsTo(User::class, 'mentioner_id');
    }
}