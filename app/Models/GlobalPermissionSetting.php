<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalPermissionSetting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'value',
        'description',
        'affects_permission_id',
    ];
    
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'affects_permission_id');
    }
}