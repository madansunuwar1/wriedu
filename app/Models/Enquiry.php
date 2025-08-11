<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [

        'uname',
        'email',
        'contact',
        'guardians',
        'contacts',
        'country',
        'education',
        'about',
        'ielts',
        'toefl',
        'ellt',
        'pte',
        'sat',
        'gre',
        'gmat',
        'other',
        'feedback',
        'counselor',
        'institution1',
        'grade1',
        'year1',
        'institution2',
        'grade2',
        'year2',
        'institution3',
        'grade3',
        'year3',
        'institution4',
        'grade4',
        'year4'
    ];
}
