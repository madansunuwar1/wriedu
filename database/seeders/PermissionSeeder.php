<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Seed permissions first
        $this->seedPermissions();

        // Create default administrator
        $this->createDefaultAdministrator();
    }

    private function seedPermissions()
    {
        $categories = [
            'Applications' => [
                ['name' => 'View applications', 'description' => 'Can view contact details', 'code' => 'view_applications', 'category' => 'Applications'],
                ['name' => 'Create applications', 'description' => 'Can create new applications', 'code' => 'create_applications', 'category' => 'Applications'],
                ['name' => 'Edit applications', 'description' => 'Can edit existing applications', 'code' => 'edit_applications', 'category' => 'Applications'],
                ['name' => 'Delete applications', 'description' => 'Can delete applications', 'code' => 'delete_applications', 'category' => 'Applications'],
                ['name' => 'Export applications', 'description' => 'Can export contact data', 'code' => 'export_applications', 'category' => 'Applications'],
            ],
            'Data_Entries' => [
                ['name' => 'View data_entries', 'description' => 'Can view contact details', 'code' => 'view_data_entries', 'category' => 'Data_Entries'],
                ['name' => 'Create data_entries', 'description' => 'Can create new data_entries', 'code' => 'create_data_entries', 'category' => 'Data_Entries'],
                ['name' => 'Edit data_entries', 'description' => 'Can edit existing data_entries', 'code' => 'edit_data_entries', 'category' => 'Data_Entries'],
                ['name' => 'Delete data_entries', 'description' => 'Can delete data_entries', 'code' => 'delete_data_entries', 'category' => 'Data_Entries'],
                ['name' => 'Export data_entries', 'description' => 'Can export contact data', 'code' => 'export_data_entries', 'category' => 'Data_Entries'],
            ],
            'Enquiries' => [
                ['name' => 'View Enquiries', 'description' => 'Can view contact details', 'code' => 'view_enquiries', 'category' => 'Enquiries'],
                ['name' => 'Create Enquiries', 'description' => 'Can create new data_entries', 'code' => 'create_enquiries', 'category' => 'Enquiries'],
                ['name' => 'Edit Enquiries', 'description' => 'Can edit existing data_entries', 'code' => 'edit_enquiries', 'category' => 'Enquiries'],
                ['name' => 'Delete Enquiries', 'description' => 'Can delete data_entries', 'code' => 'delete_enquiries', 'category' => 'Enquiries'],
                ['name' => 'Export Enquiries', 'description' => 'Can export contact data', 'code' => 'export_enquiries', 'category' => 'Enquiries'],
            ],
            'UpLoads' => [
                ['name' => 'View UpLoads', 'description' => 'Can view contact details', 'code' => 'view_uploads', 'category' => 'UpLoads'],
                ['name' => 'Create UpLoads', 'description' => 'Can create new data_entries', 'code' => 'create_uploads', 'category' => 'UpLoads'],
                ['name' => 'Edit UpLoads', 'description' => 'Can edit existing data_entries', 'code' => 'edit_uploads', 'category' => 'UpLoads'],
                ['name' => 'Delete UpLoads', 'description' => 'Can delete data_entries', 'code' => 'delete_uploads', 'category' => 'UpLoads'],
                ['name' => 'Export UpLoads', 'description' => 'Can export contact data', 'code' => 'export_uploads', 'category' => 'UpLoads'],
            ],
            'Products' => [
                ['name' => 'View Products', 'description' => 'Can view contact details', 'code' => 'view_products', 'category' => 'Products'],
                ['name' => 'Create Products', 'description' => 'Can create new data_entries', 'code' => 'create_products', 'category' => 'Products'],
                ['name' => 'Edit Products', 'description' => 'Can edit existing data_entries', 'code' => 'edit_products', 'category' => 'Products'],
                ['name' => 'Delete Products', 'description' => 'Can delete data_entries', 'code' => 'delete_products', 'category' => 'Products'],
                ['name' => 'Export Products', 'description' => 'Can export contact data', 'code' => 'export_products', 'category' => 'Products'],
            ],
            'Finances' => [
                ['name' => 'View Finances', 'description' => 'Can view contact details', 'code' => 'view_finances', 'category' => 'Finances'],
                ['name' => 'Create Finances', 'description' => 'Can create new data_entries', 'code' => 'create_finances', 'category' => 'Finances'],
                ['name' => 'Edit Finances', 'description' => 'Can edit existing data_entries', 'code' => 'edit_finances', 'category' => 'Finances'],
                ['name' => 'Delete Finances', 'description' => 'Can delete data_entries', 'code' => 'delete_finances', 'category' => 'Finances'],
                ['name' => 'Export Finances', 'description' => 'Can export contact data', 'code' => 'export_finances', 'category' => 'Finances'],
            ],
            'Leads' => [
                ['name' => 'View Leads', 'description' => 'Can view lead details', 'code' => 'view_leads', 'category' => 'Leads'],
                ['name' => 'Create Leads', 'description' => 'Can create new leads', 'code' => 'create_leads', 'category' => 'Leads'],
                ['name' => 'Edit Leads', 'description' => 'Can edit existing leads', 'code' => 'edit_leads', 'category' => 'Leads'],
                ['name' => 'Delete Leads', 'description' => 'Can delete leads', 'code' => 'delete_leads', 'category' => 'Leads'],
                ['name' => 'Convert Leads', 'description' => 'Can convert leads to opportunities', 'code' => 'convert_leads', 'category' => 'Leads'],
            ],
            'System' => [
                ['name' => 'Manage Roles', 'description' => 'Can manage roles and permissions', 'code' => 'manage_roles', 'category' => 'System'],
                ['name' => 'System Settings', 'description' => 'Can modify system settings', 'code' => 'system_settings', 'category' => 'System'],
                ['name' => 'View Audit Logs', 'description' => 'Can view system audit logs', 'code' => 'view_audit_logs', 'category' => 'System'],
            ],
            'Notices' => [
                ['name' => 'Create Notice', 'description' => 'Can manage roles and permissions', 'code' => 'create_notices', 'category' => 'notices'],
                ['name' => 'Edit Notice', 'description' => 'Can modify system settings', 'code' => 'edit_notices', 'category' => 'notices'],
                ['name' => 'View Notice', 'description' => 'Can view system audit logs', 'code' => 'view_notices', 'category' => 'notices'],
            ],
            'Users' => [
                ['name' => 'View Users', 'description' => 'Can view user profiles', 'code' => 'view_users', 'category' => 'Users'],
                ['name' => 'Edit Users', 'description' => 'Can edit user profiles', 'code' => 'edit_users', 'category' => 'Users'],
                ['name' => 'Deactivate Users', 'description' => 'Can deactivate user accounts', 'code' => 'deactivate_users', 'category' => 'Users'],
            ],
           
        ];

        foreach ($categories as $categoryName => $permissions) {
            $category = DB::table('permission_categories')->where('name', $categoryName)->first();

            if (!$category) {
                continue; // Skip if category does not exist
            }

            foreach ($permissions as $permission) {
                DB::table('permissions')->updateOrInsert(
                    ['name' => $permission['name']], 
                    [
                        'description' => $permission['description'],
                        'category_id' => $category->id,
                        'code' => $permission['code'],
                        'category' => $permission['category'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    private function createDefaultAdministrator()
    {
        // Check if admin already exists
        $existingAdmin = DB::table('users')
            ->where('email', 'admin@wrieducation.com')
            ->first();
        
        if ($existingAdmin) {
            return; // Stop if admin already exists
        }

        // Create admin user
        $adminUserId = DB::table('users')->insertGetId([
            'name' => 'System Administrator',
            'email' => 'admin@wrieducation.com',
            'password' => Hash::make('AdminPassword123!'),
            'application' => 'system',
            'email_verified_at'=> now(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get all permission IDs
        $permissionIds = DB::table('permissions')->pluck('id');

        // Prepare user permissions
        $userPermissions = $permissionIds->map(function ($permissionId) use ($adminUserId) {
            return [
                'user_id' => $adminUserId,
                'permission_id' => $permissionId,
            ];
        })->toArray();

        // Insert all permissions for the admin user
        DB::table('user_permissions')->insert($userPermissions);

        // Create or get admin role
        $adminRoleId = DB::table('roles')->insertOrIgnore([
            'name' => 'Administrator',
            'description' => 'Full system access',
        ]);

        // If role already exists, retrieve its ID
        if ($adminRoleId === 0) {
            $adminRoleId = DB::table('roles')
                ->where('name', 'Administrator')
                ->value('id');
        }

        // Assign admin role to user
        DB::table('user_roles')->insert([
            'user_id' => $adminUserId,
            'role_id' => $adminRoleId,
        ]);

        // Assign admin to both departments
        $leadsDept = DB::table('departments')->where('name', 'Leads Department')->first();
        $applicationsDept = DB::table('departments')->where('name', 'Applications Department')->first();

        if ($leadsDept) {
            DB::table('user_departments')->insert([
                'user_id' => $adminUserId,
                'department_id' => $leadsDept->id,
            ]);
        }

        if ($applicationsDept) {
            DB::table('user_departments')->insert([
                'user_id' => $adminUserId,
                'department_id' => $applicationsDept->id,
            ]);
        }
    }
}