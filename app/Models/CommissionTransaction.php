<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_id',
        'application_id',
        'user_id',
        'type',
        'amount',
        'exchange_rate',
        'paid',
        'status',
        'due_date',
        'commissionpayable_id',
    ];

    protected $casts = [
        'paid' => 'string',
        'due_date' => 'date',
    ];

    // Define relationships
   

   

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function commission()
{
    return $this->belongsTo(Comission::class, 'commission_id');
    // Note the spelling 'Comission' if that's your actual model name
}
public function commissionTransactions()
    {
        return $this->hasOne(CommissionTransaction::class);
    }
     public function getMatchedApplication()
    {
        return Application::where('university', $this->university)
                         ->where('english', $this->product)
                         ->first();
    }
    
    /**
     * Get commission payable record based on university and product matching
     */
    public function getCommissionPayable()
    {
        return CommissionPayable::where('university', $this->university)
                               ->where('product', $this->product)
                               ->first();
    }

    
   
    public function application()
{
    return $this->belongsTo(Application::class, 'application_id');
}

public function commissionPayable()
{
    return $this->belongsTo(CommissionPayable::class, 'commissionpayable_id');
}
}
