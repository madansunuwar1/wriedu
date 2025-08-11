<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Database\Eloquent\Model;

class Commentadd extends Model
{
    use HasFactory;

    protected $fillable = [

        'applications'
           
    ];
    public function application()
{
    return $this->belongsTo(Application::class);
}
    
}
