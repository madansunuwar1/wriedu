<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'start_date',
        'end_date',
        'color',
        'notice_id',
        'type',
    ];

    // Use $casts instead of $dates (Laravel 8+)
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Relationship with Notice
     */
    public function notice()
    {
        return $this->belongsTo(Notice::class);
    }

    /**
     * Accessor for formatted start date
     */
    public function getFormattedStartDateAttribute()
    {
        if (!$this->start_date) {
            return null;
        }
        
        // Ensure it's a Carbon instance
        $date = $this->start_date instanceof Carbon 
            ? $this->start_date 
            : Carbon::parse($this->start_date);
            
        return $date->format('Y-m-d');
    }

    /**
     * Accessor for formatted end date
     */
    public function getFormattedEndDateAttribute()
    {
        if (!$this->end_date) {
            return null;
        }
        
        // Ensure it's a Carbon instance
        $date = $this->end_date instanceof Carbon 
            ? $this->end_date 
            : Carbon::parse($this->end_date);
            
        return $date->format('Y-m-d');
    }

    /**
     * Check if event is multi-day
     */
    public function isMultiDay()
    {
        if (!$this->start_date || !$this->end_date) {
            return false;
        }

        // Ensure both are Carbon instances
        $startDate = $this->start_date instanceof Carbon 
            ? $this->start_date 
            : Carbon::parse($this->start_date);
            
        $endDate = $this->end_date instanceof Carbon 
            ? $this->end_date 
            : Carbon::parse($this->end_date);

        return $startDate->format('Y-m-d') !== $endDate->format('Y-m-d');
    }

    /**
     * Get color class for display
     */
    public function getColorClassAttribute()
    {
        $colors = [
            'danger' => 'text-danger',
            'warning' => 'text-warning',
            'primary' => 'text-primary',
            'success' => 'text-success',
        ];

        return $colors[$this->color] ?? 'text-primary';
    }
}