<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends Controller
{
    /**
     * Get the authenticated user along with their role and all effective permissions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        // Ensure the user has roles loaded to prevent extra queries if already loaded
        $user->loadMissing('roles');
        $role = $user->roles->first()->name ?? 'User'; // Default to 'User' if no role is found

        // Get all permissions assigned to the user (either directly or via roles)
        // using the Spatie package. Then, use pluck() to get a simple array of
        // permission names, which is what the frontend expects.
       $permissions = $user->getAllPermissions()->pluck('code');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'last' => $user->last,
                'email' => $user->email,
                'avatar' => $user->avatar ? asset('storage/avatars/' . $user->avatar) : null,
                'role' => $role,
            ],
            // Return the permissions array fetched dynamically from the database
            'permissions' => $permissions,
        ]);
    }
}