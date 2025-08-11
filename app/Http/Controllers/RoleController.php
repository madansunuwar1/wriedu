<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::withCount('permissions')->get();
        return view('backend.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
       $permissions = Permission::with('category')
    ->get()
    ->groupBy(function($permission) {
        return ($permission->category && is_object($permission->category)) ? $permission->category->name : 'Uncategorized';
    });
        return view('backend.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
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
    
            return redirect()->route('backend.roles.index')
                ->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating role: ' . $e->getMessage())
                ->withInput();
        }
    }
    

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions.category');
        return view('backend.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($id) 
{
    $role = Role::with('permissions')->findOrFail($id); // Ensure $role is fetched
    $permissions = Permission::with('category')
    ->get()
    ->groupBy(function($permission) {
        return ($permission->category && is_object($permission->category)) ? $permission->category->name : 'Uncategorized';
    });

    return view('backend.roles.update', compact('role', 'permissions'));
}


    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, $id)
{
    $role = Role::findOrFail($id);
    
    $validated = $request->validate([
        'name' => 'required|string|max:100|unique:roles,name,' . $id,
        'description' => 'nullable|string',
        'permissions' => 'array',
    ]);
    
    DB::beginTransaction();
    
    try {
        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);
        
        $role->permissions()->sync(isset($validated['permissions']) ? $validated['permissions'] : []);
        
        DB::commit();
        
        return redirect()->route('backend.roles.index')
            ->with('success', 'Role updated successfully');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error updating role: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->is_system_role) {
            return back()->with('error', 'System roles cannot be deleted');
        }
        
        try {
            $role->delete();
            return redirect()->route('backend.roles.index')
                ->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }
    
    /**
     * Get permissions for a role (API method)
     */
    public function getPermissions(Role $role)
    {
        return response()->json([
            'permissions' => $role->permissions->pluck('id')
        ]);
    }
}