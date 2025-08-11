<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleApiController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->get();
        return response()->json($roles);
    }

 public function create()
{
    $permissions = Permission::with('category')->get()->map(function ($permission) {
        try {
            // Initialize category name
            $categoryName = 'Uncategorized';
            
            // Check if category relationship exists and is loaded
            if ($permission->relationLoaded('category') && $permission->category !== null) {
                // Handle object category
                if (is_object($permission->category) && property_exists($permission->category, 'name')) {
                    $categoryName = $permission->category->name;
                }
                // Handle string category (in case it's stored as string)
                elseif (is_string($permission->category)) {
                    $categoryName = $permission->category;
                }
            }
            
            $permission->category_name = $categoryName;
            return $permission;
            
        } catch (\Exception $e) {
            \Log::error('Error processing permission category: ' . $e->getMessage(), [
                'permission_id' => $permission->id ?? 'unknown',
                'category_data' => $permission->category ?? 'null'
            ]);
            
            $permission->category_name = 'Uncategorized';
            return $permission;
        }
    })->groupBy('category_name');

    return response()->json($permissions);
}
public function getAllPermissions()
{
    $permissions = Permission::with('category')->get(); // Adjust based on your model
    return response()->json(['permissions' => $permissions]);
}

// In your RoleController, modify the show method or create a new method
public function showupdate($id)
{
    $role = Role::with('permissions')->findOrFail($id);
    $allPermissions = Permission::with('category')->get(); // Get all permissions
    
    return response()->json([
        'id' => $role->id,
        'name' => $role->name,
        'description' => $role->description,
        'permissions' => $role->permissions, // Role's current permissions
        'all_permissions' => $allPermissions, // All available permissions
    ]);
}

 /**
     * Get all permissions for the currently authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserPermissions(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        if (!$user) {
            // This case should ideally not be hit if auth:sanctum middleware is working
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Using the Spatie/laravel-permission package to get all permissions
        // This includes direct permissions and permissions inherited from roles.
        $permissions = $user->getAllPermissions()->pluck('name');
        
        // Return the permissions in the format the frontend expects
        return response()->json([
            'permissions' => $permissions,
        ]);
    }





    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'array',
        ]);

        DB::beginTransaction();

        try {
            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            if (isset($validated['permissions'])) {
                $role->permissions()->attach($validated['permissions']);
            }

            DB::commit();

            return response()->json($role, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error creating role: ' . $e->getMessage()], 500);
        }
    }

    public function show(Role $role)
    {
        $role->load('permissions.category');
        return response()->json($role);
    }

    public function edit(Role $role)
    {
        $permissions = Permission::with('category')->get()->groupBy(function($permission) {
            return $permission->category ? $permission->category->name : 'Uncategorized';
        });

        return response()->json([
            'role' => $role->load('permissions'),
            'permissions' => $permissions
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'array',
        ]);

        DB::beginTransaction();

        try {
            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            $role->permissions()->sync($validated['permissions'] ?? []);

            DB::commit();

            return response()->json($role);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error updating role: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Role $role)
    {
        if ($role->is_system_role) {
            return response()->json(['error' => 'System roles cannot be deleted'], 403);
        }

        try {
            $role->delete();
            return response()->json(['message' => 'Role deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting role: ' . $e->getMessage()], 500);
        }
    }
}