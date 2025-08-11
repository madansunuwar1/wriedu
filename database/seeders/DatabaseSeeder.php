<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionCategorySeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            DepartmentSeeder::class,
            
        ]);
    }
}