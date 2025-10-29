<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'HR',
                'code' => 'HR',
            ],
            [
                'name' => 'IT',
                'code' => 'IT',
            ],
            [
                'name' => 'Sales',
                'code' => 'Sales',
            ],
            [
                'name' => 'Marketing',
                'code' => 'Marketing',
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(['name' => $department['name']], $department);
        }
    }
}
