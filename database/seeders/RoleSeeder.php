<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Define default roles
        $roles = [
            ['name' => 'Administrator', 'description' => 'Full system access', 'is_system_role' => true],
            ['name' => 'Manager', 'description' => 'Department-level management access', 'is_system_role' => true],
            ['name' => 'Standard User', 'description' => 'Regular user access to core CRM features', 'is_system_role' => true],
            ['name' => 'Read-Only User', 'description' => 'View-only access to CRM data', 'is_system_role' => true],
            ['name' => 'Leads Manager', 'description' => 'Manager for the Leads Department', 'is_system_role' => false],
            ['name' => 'Applications Manager', 'description' => 'Manager for the Applications Department', 'is_system_role' => false],
            ['name' => 'Leads User', 'description' => 'User with access to Leads Department features', 'is_system_role' => false],
            ['name' => 'Applications User', 'description' => 'User with access to Applications Department features', 'is_system_role' => false],
            
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']], // Search by name
                [
                    'description' => $role['description'],
                    'is_system_role' => $role['is_system_role'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Assign permissions to roles
        $this->assignPermissions();
        
        // Associate roles with departments
        $this->associateRolesWithDepartments();
    }

    private function assignPermissions()
    {
        // Fetch roles
        $adminRole = DB::table('roles')->where('name', 'Administrator')->first();
        $managerRole = DB::table('roles')->where('name', 'Manager')->first();
        $standardRole = DB::table('roles')->where('name', 'Standard User')->first();
        $readOnlyRole = DB::table('roles')->where('name', 'Read-Only User')->first();
        $leadsManagerRole = DB::table('roles')->where('name', 'Leads Manager')->first();
        $applicationsManagerRole = DB::table('roles')->where('name', 'Applications Manager')->first();
        $leadsUserRole = DB::table('roles')->where('name', 'Leads User')->first();
        $applicationsUserRole = DB::table('roles')->where('name', 'Applications User')->first();

        if (!$adminRole || !$managerRole || !$standardRole || !$readOnlyRole) {
            return;
        }

        // Assign permissions
        $allPermissions = DB::table('permissions')->get();
        $managerPermissions = DB::table('permissions')
            ->whereNotIn('code', ['system_settings', 'manage_roles'])
            ->get();
        $standardPermissions = DB::table('permissions')
            ->whereIn('code', [
                'view_contacts', 'create_contacts', 'edit_contacts',
                'view_leads', 'create_leads', 'edit_leads', 'convert_leads'
            ])
            ->get();
        $viewPermissions = DB::table('permissions')
            ->where('code', 'like', 'view_%')
            ->get();
            
        // Lead-specific permissions
        $leadPermissions = DB::table('permissions')
            ->where('category', 'Leads')
            ->get();
            
        // Application-specific permissions
        $applicationPermissions = DB::table('permissions')
            ->where('category', 'Applications')
            ->get();

        // Assign to Administrator
        foreach ($allPermissions as $permission) {
            DB::table('role_permissions')->updateOrInsert(
                ['role_id' => $adminRole->id, 'permission_id' => $permission->id]
            );
        }

        // Assign to Manager
        foreach ($managerPermissions as $permission) {
            DB::table('role_permissions')->updateOrInsert(
                ['role_id' => $managerRole->id, 'permission_id' => $permission->id]
            );
        }

        // Assign to Standard User
        foreach ($standardPermissions as $permission) {
            DB::table('role_permissions')->updateOrInsert(
                ['role_id' => $standardRole->id, 'permission_id' => $permission->id]
            );
        }

        // Assign to Read-Only User
        foreach ($viewPermissions as $permission) {
            DB::table('role_permissions')->updateOrInsert(
                ['role_id' => $readOnlyRole->id, 'permission_id' => $permission->id]
            );
        }
        
        // Assign to Leads Manager
        if ($leadsManagerRole) {
            foreach ($leadPermissions as $permission) {
                DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => $leadsManagerRole->id, 'permission_id' => $permission->id]
                );
            }
        }
        
        // Assign to Applications Manager
        if ($applicationsManagerRole) {
            foreach ($applicationPermissions as $permission) {
                DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => $applicationsManagerRole->id, 'permission_id' => $permission->id]
                );
            }
        }
        
        // Assign to Leads User (view, create, edit only)
        if ($leadsUserRole) {
            foreach ($leadPermissions as $permission) {
                if (strpos($permission->code, 'view_') === 0 || 
                    strpos($permission->code, 'create_') === 0 || 
                    strpos($permission->code, 'edit_') === 0) {
                    DB::table('role_permissions')->updateOrInsert(
                        ['role_id' => $leadsUserRole->id, 'permission_id' => $permission->id]
                    );
                }
            }
        }
        
        // Assign to Applications User (view, create, edit only)
        if ($applicationsUserRole) {
            foreach ($applicationPermissions as $permission) {
                if (strpos($permission->code, 'view_') === 0 || 
                    strpos($permission->code, 'create_') === 0 || 
                    strpos($permission->code, 'edit_') === 0) {
                    DB::table('role_permissions')->updateOrInsert(
                        ['role_id' => $applicationsUserRole->id, 'permission_id' => $permission->id]
                    );
                }
            }
        }
    }
    
    private function associateRolesWithDepartments()
    {
        // This is for demonstration. In a real system, you might want to create a role_departments table
        // to associate roles with specific departments.
        
        // For now, we'll document which roles are meant for which departments:
        // - Leads Manager & Leads User -> Leads Department
        // - Applications Manager & Applications User -> Applications Department
        // - Administrator, Manager, Standard User, Read-Only User -> All departments
    }
}