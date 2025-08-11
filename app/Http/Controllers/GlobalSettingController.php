<?php

// app/Http/Controllers/Admin/GlobalSettingsController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;

class GlobalSettingsController extends Controller
{
    public function index()
    {
        $this->authorize('view_global_settings');
        
        $settings = GlobalSetting::all()->groupBy('group');
        
        return view('admin.settings.global', compact('settings'));
    }
    
    public function update(Request $request)
    {
        $this->authorize('update_global_settings');
        
        $validatedData = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'required',
        ]);
        
        foreach ($validatedData['settings'] as $key => $value) {
            $setting = GlobalSetting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }
        
        return redirect()->back()->with('success', 'Global settings updated successfully.');
    }
    
    public function getRoleDefaultPermissions($roleId)
    {
        $this->authorize('view_role_permissions');
        
        $defaultSettings = GlobalSetting::where('key', 'default_role_permissions')
            ->where('group', 'permissions')
            ->first();
            
        if ($defaultSettings && isset($defaultSettings->value[$roleId])) {
            return response()->json($defaultSettings->value[$roleId]);
        }
        
        return response()->json([]);
    }
    
    public function setRoleDefaultPermissions(Request $request, $roleId)
    {
        $this->authorize('update_role_permissions');
        
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $defaultSettings = GlobalSetting::firstOrCreate(
            [
                'key' => 'default_role_permissions',
                'group' => 'permissions'
            ],
            [
                'value' => [],
                'description' => 'Default permissions for each role'
            ]
        );
        
        $values = $defaultSettings->value;
        $values[$roleId] = $validated['permissions'];
        
        $defaultSettings->update(['value' => $values]);
        
        return response()->json(['success' => true]);
    }
}