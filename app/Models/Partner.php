<?php
// app/Models/Partner.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_name',
        'email',
        'Address',
        'contact_no',
        'agency_counselor_email'
    ];


    public function applications()
    {
        return $this->hasMany(Application::class);
    }

}