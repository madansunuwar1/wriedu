<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionPayable extends Model
{
    protected $table = 'commission_payable';  // Specify the table name
    
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
    ];
    
    protected $casts = [
        'commission_types' => 'array',
        'has_progressive_commission' => 'boolean',
        'has_bonus_commission' => 'boolean',
        'has_incentive_commission' => 'boolean',
    ];
     public function getMatchedApplication()
    {
        return Application::where('university', $this->university)
                         ->where('english', $this->product)
                         ->first();
    }
    
    /**
     * Get commission transaction based on university and product matching
     */
    public function getCommissionTransaction()
    {
        return CommissionTransaction::where('university', $this->university)
                                  ->where('product', $this->product)
                                  ->first();
    }
    public function commissionTransactions()
{
    return $this->hasMany(CommissionTransaction::class, 'commissionpayable_id');
}
    
}