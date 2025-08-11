<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission; // Import the Permission class
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserRoleController extends Controller
{
    /**
     * Show role assignment page for a user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');

        return view('user_roles.edit', compact('user', 'roles'));
    }

    /**
     * Update roles for a user
     */
    public function updateRole(Request $request, $id)
    {
        try {
            $request->validate([
                'role' => 'required|string'
            ]);

            $user = User::findOrFail($id);
            $role = Role::where('name', $request->role)->firstOrFail();

            // Sync the roles
            $user->roles()->sync([$role->id]);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Role validation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Invalid role data',
                'errors' => $e->errors()
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('User or Role not found: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'User or Role not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Role update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignPermission(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        try {
            $user = User::findOrFail($validated['user_id']);
            $permission = Permission::findOrFail($validated['permission_id']);

            // Check if permission is already assigned
            if (!$user->permissions()->where('permissions.id', $permission->id)->exists()) {
                $user->permissions()->attach($permission->id);
            }

            return response()->json([
                'message' => 'Permission assigned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error assigning permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign a role to a user (API method)
     */
    public function assignRole(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id', // Corrected to 'roles'
        ]);

        try {
            $user = User::findOrFail($validated['user_id']);
            $role = Role::findOrFail($validated['role_id']); // Corrected to 'Role'

            // Check if role is already assigned
            if (!$user->roles()->where('roles.id', $role->id)->exists()) {
                $user->roles()->attach($role->id);
            }

            return response()->json([
                'message' => 'Role assigned successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error assigning role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a role from a user (API method)
     */
    public function removeRole(Request $request, User $user, Role $role)
    {
        try {
            $user->roles()->detach($role->id);

            return response()->json([
                'message' => 'Role removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error removing role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get roles for a user (API method)
     */
    public function getRoles(User $user)
    {
        return response()->json([
            'roles' => $user->roles
        ]);
    }
}
