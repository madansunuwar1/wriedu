<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory;

    /**
     * Tell Eloquent that the primary key is NOT an auto-incrementing integer.
     * THIS IS THE MOST IMPORTANT FIX.
     */
    public $incrementing = false;

    /**
     * Tell Eloquent that the primary key's data type is a string.
     */
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'message',
        'content',
        'link',
        'read',
        'type',
        'data'
    ];

    protected $casts = [
        'read' => 'boolean',
        'data' => 'array',
    ];

    /**
     * Your boot method is correct for generating UUIDs and should be kept.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}