<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'application_id',
        'commission',
        'exchange',
    ];

    public function application()
{
    return $this->belongsTo(Application::class);
}

/**
     * Get the commission associated with the store.
     */
    public function comission()
    {
        return $this->hasOne(Comission::class);
    }

    /**
     * Sync commission data from related Comission model
     * This ensures commission data is visible in the store
     */
    public function syncCommissionData()
    {
        $comissionRecord = $this->comission;
        
        if ($comissionRecord) {
            // Update the store's commission field with paid value from Comission
            $this->commission = $comissionRecord->paid;
            $this->save();
            
            return true;
        }
        
        return false;
    }
}