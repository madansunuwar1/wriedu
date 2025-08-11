<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        // Create main departments
        $departments = [
            ['name' => 'Leads Department', 'parent_department_id' => null],
            ['name' => 'Applications Department', 'parent_department_id' => null],
            // Add more departments as needed
        ];

        foreach ($departments as $department) {
            DB::table('departments')->updateOrInsert(
                ['name' => $department['name']],
                [
                    'parent_department_id' => $department['parent_department_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}