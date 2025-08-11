<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\support\Collection;
use Carbon\Carbon;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    // Gender constants
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    protected $fillable = [
        'name',
        'last',
        'email',
        'application',
        'password',
        'role_id',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'avatar',
        'gender', // Will only store 'male' or 'female'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Check if the user is currently online.
     *
     * @return bool
     */
    public function isOnline()
    {
        // Check if the user's last activity was within the last 5 minutes
        return $this->last_activity && Carbon::parse($this->last_activity)->diffInMinutes(Carbon::now()) < 5;
    }

    /**
     * Automatically attach a default role to a user when they are created
     * and assign a random avatar
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // Assign default role
            $defaultRole = Role::where('name', 'default-role')->first();
            if ($defaultRole) {
                $user->roles()->attach($defaultRole->id);
            }

            // Assign random avatar
            $user->assignRandomAvatar();
        });
    }


    public function applications()
    {
        return $this->belongsToMany(Application::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedLeads()
    {
        return $this->hasMany(Lead::class, 'user_id');
    }

    /**
     * Assign a random avatar to the user
     */

    public static function getGenders()
    {
        return [
            self::GENDER_MALE => 'Male',
            self::GENDER_FEMALE => 'Female',
        ];
    }

    public function assignRandomAvatar()
    {
        $fullName = strtolower(trim($this->name . ' ' . $this->last));

        if ($fullName === 'rabin pyakurel') {
            // Male user with special avatar
            $this->avatar = 'rabin.jpg';
        } elseif ($fullName === 'susma shrestha') {
            // Female user with special avatar
            $this->avatar = 'susma.jpg';
        } else {
            $maleAvatars = [
                'male-1.jpg',
                'male-2.jpg',
                'male-3.jpg',
                'male-4.jpg',
                'male-5.jpg',
                'male-6.jpg',
                'male-7.jpg',
                'male-8.jpg',
                'male-9.jpg',
            ];

            $femaleAvatars = [
                'female-1.jpg',
                'female-2.jpg',
                'female-3.jpg',
            ];

            $avatars = $this->gender === self::GENDER_FEMALE ? $femaleAvatars : $maleAvatars;
            $this->avatar = $avatars[array_rand($avatars)];
        }

        $this->save();
    }


    public function mentions()
    {
        return $this->hasMany(CommentMention::class, 'mentioned_user_id');
    }

    public function mentionsMade()
    {
        return $this->hasMany(CommentMention::class, 'mentioner_id');
    }

    public function seenNotices()
    {
        return $this->belongsToMany(Notice::class, 'notice_user')->withPivot('seen_at');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'from_user_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'to_user_id', 'id');
    }

    public function unreadMessages()
    {
        return $this->receivedMessages()->where('read', false);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class, 'from_user_id', 'id')
            ->orWhere('to_user_id', $this->id)
            ->latest();
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * Get the roles assigned to the user
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Get the departments the user belongs to
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'user_departments');
    }

    /**
     * Get direct permissions for the user
     */
    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

     /**
     * [THE MAIN FIX]
     * This custom method replaces the missing Spatie function. It gets all
     * permissions a user has, both from their roles and assigned directly.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPermissions(): Collection
    {
        // Get all permissions from the user's roles.
        // We eager-load 'permissions' for better performance.
        $permissionsFromRoles = $this->roles->loadMissing('permissions')->flatMap(function ($role) {
            return $role->permissions;
        });

        // Get all permissions assigned directly to the user.
        $directPermissions = $this->directPermissions;

        // Merge the two collections and ensure permissions are unique by their ID.
        return $permissionsFromRoles->merge($directPermissions)->unique('id');
    }

    /**
     * Get all permissions for the user (both from roles and direct)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permissionCode)
    {
        // Check for permission through roles
        $hasRolePermission = $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionCode) {
                $query->where('code', $permissionCode);
            })
            ->exists();

        // Check for direct permission
        $hasDirectPermission = $this->directPermissions()
            ->where('code', $permissionCode)
            ->exists();

        return $hasRolePermission || $hasDirectPermission;
    }

    /**
     * Check if user has any of the specified permissions
     */
    public function hasAnyPermission(array $permissionCodes)
    {
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionCodes) {
                $query->whereIn('code', $permissionCodes);
            })
            ->exists() || $this->directPermissions()
            ->whereIn('code', $permissionCodes)
            ->exists();
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleNames): bool
    {
        // Ensure $roleNames is always an array for consistent checking.
        if (is_string($roleNames)) {
            $roleNames = [$roleNames];
        }

        // Use whereIn to check if the user has any of the roles in the provided array.
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }
}
