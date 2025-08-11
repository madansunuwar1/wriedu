<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Lead extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'locations',
        'lastqualification',
        'courselevel',
        'passed',
        'gpa',
        'englishTest',
        'academic',
        'higher',
        'less',
        'score',
        'englishscore',
        'englishtheory',
        'otherScore',
        'country',
        'location',
        'university',
        'course',
        'intake',
        'offerss',
        'source',
        'otherDetails',
        'sources',
        'link',
        'avatar',
        'status',
        'created_by',
    ];

      public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function assignRandomAvatar()
    {
        // Combined pool of avatars without special cases
        $avatars = [
            'male-1.jpg',
            'male-2.jpg',
            'male-3.jpg',
            'male-4.jpg',
            'male-5.jpg',
            'male-6.jpg',
            'male-7.jpg',
            'male-8.jpg',
            'male-9.jpg',
            'female-1.jpg',
            'female-2.jpg',
            'female-3.jpg',
        ];
        $this->avatar = $avatars[array_rand($avatars)];
        $this->save();
    }
    // In Lead.php (Lead model)
    public function application()
    {
        return $this->hasOne(Application::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(LeadComment::class);
    }

    public function lead_comments()
    {
        return $this->hasMany(LeadComment::class, 'lead_id');
    }
    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }
       /**
     * Get the lead's "true" status for display.
     *
     * This is an Accessor. It intercepts any call to `$lead->status` and runs this logic.
     * It ensures that if a lead is forwarded, its status is always derived from its most
     * recent application, guaranteeing the front-end always shows the correct data.
     *
     * @param  string  $originalStatus The value from the 'status' column in the database.
     * @return string
     */
    public function getStatusAttribute($originalStatus)
    {
        // The accessor checks two conditions:
        // 1. Has the lead been forwarded? (is_forwarded flag)
        // 2. Have the applications been loaded into the model? (to prevent extra queries)
        if ($this->is_forwarded && $this->relationLoaded('applications')) {
            
            // A lead can have multiple applications. The most relevant one is the newest.
            $latestApplication = $this->applications->sortByDesc('created_at')->first();

            // If a related application exists, its status is the definitive source of truth.
            if ($latestApplication) {
                return $latestApplication->status;
            }
        }

        // If the lead has not been forwarded, or if no applications are loaded,
        // we simply return its own status from the database.
        return $originalStatus;
    }
}
