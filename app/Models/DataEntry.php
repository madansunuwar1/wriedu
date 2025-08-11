<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class DataEntry extends Model
{
    use HasFactory;

    

    protected $fillable = [
        'newUniversity',
        'newLocation',
        'newCourse',
        'newIntake',
        'newScholarship',
        'newAmount',
        'newIelts',
        'newpte',
        'newPgIelts',
        'newPgPte',
        'newug',
        'newpg',
        'newgpaug',
        'newgpapg',
        'newtest',
        'country',
        'requireddocuments',
        'level',
      
        
    ];

    

    

}
