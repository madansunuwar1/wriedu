<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommissionHistory extends Model
{
    use HasFactory;

    protected $table = 'commission_history';

    protected $fillable = [
        'application_id',
        'student_name',
        'university',
        'intake',
        'english',
        'type', // 'receivable' or 'payable'
        'commission_amount',
        'bonus_amount',
        'incentive_amount',
        'total_usd',
        'exchange_rate',
        'total_npr',
        'paid_amount',
        'status', // 'received', 'paid', 'completed'
        'received_at',
        'paid_at',
        'notes'
    ];

    protected $casts = [
        'commission_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'incentive_amount' => 'decimal:2',
        'total_usd' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'total_npr' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'received_at' => 'datetime',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'application_id', 'application_id');
    }

    // Accessors
    public function getTotalNprAttribute($value)
    {
        return $this->total_usd * $this->exchange_rate;
    }

    public function getBalanceAttribute()
    {
        return $this->total_npr - $this->paid_amount;
    }

    // Scopes
    public function scopeReceivable($query)
    {
        return $query->where('type', 'receivable');
    }

    public function scopePayable($query)
    {
        return $query->where('type', 'payable');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['received', 'paid', 'completed']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}