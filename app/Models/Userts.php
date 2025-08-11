

<?php



use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail  // Add MustVerifyEmail here
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'last',
        'email',
        'application',
        'password',
    ];

    // ... rest of your model code
}