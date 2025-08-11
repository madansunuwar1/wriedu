<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'university', 'product', 'course', 'intake',
        'location', 'lastqualification', 'passed', 'gpa', 'english',
        'englishTest', 'higher', 'less', 'score', 'englishscore',
        'englishtheory', 'additionalinfo', 'notes',
        'country', 'source', 'partnerDetails', 'otherDetails', 'created_by',
        'searchField', 'customSearchField', 'courseSearchField',
        'user_id', 'fee', 'commission', 'exchange_rate_type',
        'status', 'lead_id', 'canceled_at','avatar','original_application_id', 'level',  'student_id',
        'birth_date', 'partner_id',
    ];

    protected $dates = [
        'canceled_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
public function assignRandomAvatar() {
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

    public function application() 
    {
        return $this->belongsTo(Application::class);
    }

    public function assignedUser() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function store() 
    {
        return $this->hasOne(Store::class);
    }
    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}
 
    public function setUniversityAttribute($value)
{
    $this->attributes['university'] = trim($value);
}

public function setEnglishAttribute($value)
{
    $this->attributes['english'] = trim($value);
}


public function commission_transaction()
{
    return $this->hasOne(CommissionTransaction::class, 'application_id', 'id');
    // Adjust the foreign key and local key as needed based on your database structure
}
public function commission_transactions()
{
    return $this->hasMany(CommissionTransaction::class);
}

    public function partner()
    {
        // THIS IS THE FIX.
        // By removing the extra arguments, Laravel automatically
        // looks for the 'partner_id' column.
        return $this->belongsTo(Partner::class);
    }

      // ADD THIS METHOD TO DEFINE THE RELATIONSHIP
    /**
     * Get the commission history records for the application.
     */
    public function commissionHistories()
    {
        // An Application has many CommissionHistory records.
        // Laravel will automatically look for `application_id` in the `commission_histories` table.
        return $this->hasMany(CommissionHistory::class);
    }
}