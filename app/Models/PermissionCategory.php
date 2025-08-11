<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionCategory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'display_order',
    ];
    
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'category_id');
    }
}