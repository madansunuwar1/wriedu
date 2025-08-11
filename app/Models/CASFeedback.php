<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CASFeedback extends Model
{
    use HasFactory;

    protected $table = 'cas_feedbacks';

    protected $fillable = [
        'application_id',
        'user_id',
        'feedback_type',
        'priority',
        'subject',
        'feedback',
        'status',
        'entry_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the application that owns the CAS feedback.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the user that created the CAS feedback.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include feedback of a given type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('feedback_type', $type);
    }

    /**
     * Scope a query to only include feedback with a given priority.
     */
    public function scopeOfPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include feedback with a given status.
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include open feedback.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    /**
     * Scope a query to only include closed feedback.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'Closed');
    }

    /**
     * Get priority color class for badges
     */
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'Critical' => 'bg-danger-subtle text-danger border-danger',
            'High' => 'bg-warning-subtle text-warning border-warning',
            'Medium' => 'bg-primary-subtle text-primary border-primary',
            'Low' => 'bg-secondary-subtle text-secondary border-secondary',
            default => 'bg-secondary-subtle text-secondary border-secondary',
        };
    }

    /**
     * Get status color class for badges
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Closed' => 'bg-success-subtle text-success border-success',
            'Resolved' => 'bg-info-subtle text-info border-info',
            'In Progress' => 'bg-warning-subtle text-warning border-warning',
            'Open' => 'bg-secondary-subtle text-secondary border-secondary',
            default => 'bg-secondary-subtle text-secondary border-secondary',
        };
    }

    /**
     * Check if feedback is editable
     */
    public function isEditableBy($user)
    {
        return $this->user_id === $user->id || $user->hasRole('admin');
    }

    /**
     * Check if feedback is high priority
     */
    public function isHighPriority()
    {
        return in_array($this->priority, ['High', 'Critical']);
    }

    /**
     * Check if feedback is open
     */
    public function isOpen()
    {
        return $this->status === 'Open';
    }

    /**
     * Check if feedback is closed
     */
    public function isClosed()
    {
        return $this->status === 'Closed';
    }
}