<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comission extends Model
{
    protected $fillable = [
        'university',
        'product',
        'partner',
        'has_progressive_commission',
        'progressive_commission',
        'has_bonus_commission',
        'bonus_commission',
        'has_incentive_commission',
        'incentive_commission',
        'commission_types',
        'applications_id',
        'store_id',
        'paid', // Add this field if it's missing
        'other', // Add any other fields you're trying to update
        'bonus',
        'incentive'
    ];

    /**
     * Cast commission_types to array if stored as JSON
     */
    protected $casts = [
        'commission_types' => 'array',
        'has_progressive_commission' => 'boolean',
        'has_bonus_commission' => 'boolean',
        'has_incentive_commission' => 'boolean',
    ];

    /**
     * Many-to-Many relationship with CommissionType
     */
    public function commissionTypes()
    {
        return $this->belongsToMany(CommissionType::class, 'comission_commission_type');
    }

    /**
     * Comission belongs to a Store
     */
   

    /**
     * Comission belongs to an Application
     */
    public function application()
    {
        return $this->belongsTo(Application::class, 'applications_id');
    }

    /**
     * Scope: Match application by trimmed university and product fields
     */
    public function scopeMatchingApplication($query, Application $application)
    {
        return $query->where(DB::raw('TRIM(university)'), trim($application->university))
                     ->where(DB::raw('TRIM(product)'), trim($application->english));
    }

    /**
     * Automatically update the related Store's commission when this is saved
     */
    protected static function booted()
    {
        static::saved(function ($comission) {
            if ($comission->store) {
                $comission->store->commission = $comission->paid ?? 0;
                $comission->store->save();
            }
        });
    }
}