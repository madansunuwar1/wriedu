<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'parent_department_id',
        'leads_id',
    ];
    
    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'parent_department_id');
    }
    
    public function childDepartments()
    {
        return $this->hasMany(Department::class, 'parent_department_id');
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_departments');
    }

    
}