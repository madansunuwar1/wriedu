<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Applications', 'description' => 'Permissions related to contact management', 'display_order' => 10],
            ['name' => 'Leads', 'description' => 'Permissions related to lead management', 'display_order' => 20],
            ['name' => 'Opportunities', 'description' => 'Permissions related to opportunity management', 'display_order' => 30],
            ['name' => 'Documents', 'description' => 'Permissions related to document management', 'display_order' => 40],
            ['name' => 'Reports', 'description' => 'Permissions related to reporting', 'display_order' => 50],
            ['name' => 'System', 'description' => 'Permissions related to system configuration', 'display_order' => 60],
            ['name' => 'Users', 'description' => 'Permissions related to user management', 'display_order' => 70],
            ['name' => 'Data_Entries', 'description' => 'Permissions related to user management', 'display_order' => 80],
            ['name' => 'Enquiries', 'description' => 'Permissions related to user management', 'display_order' => 90],
            ['name' => 'UpLoads', 'description' => 'Permissions related to user management', 'display_order' => 100],
            ['name' => 'Products', 'description' => 'Permissions related to user management', 'display_order' => 110],
            ['name' => 'Finances', 'description' => 'Permissions related to user management', 'display_order' => 120],
            ['name' => 'Notices', 'description' => 'Permissions related to user management', 'display_order' => 130],
        ];

        foreach ($categories as $category) {
            DB::table('permission_categories')->updateOrInsert(
                ['name' => $category['name']], // Check by name
                [
                    'description' => $category['description'],
                    'display_order' => $category['display_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}