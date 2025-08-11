<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emails';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gmail_account_id',
        'message_id',
        'subject',
        'sender',
        'recipient',
        'body',
        'raw_content',
        'received_at',
        'is_read',
        'labels'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'received_at' => 'datetime',
        'is_read' => 'boolean',
        'labels' => 'array'
    ];

    /**
     * Get the Gmail account that owns the email.
     *
     * @return BelongsTo
     */
    public function gmailAccount(): BelongsTo
    {
        return $this->belongsTo(GmailAccount::class);
    }

    /**
     * Scope a query to only include unread emails.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}