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
                'division_id' => 1
            ],
            [
                'name' => 'IT',
                'code' => 'IT',
                'division_id' => 2
            ],
            [
                'name' => 'Sales',
                'code' => 'Sales',
                'division_id' => 2
            ],
            [
                'name' => 'Marketing',
                'code' => 'Marketing',
                'division_id' => 1
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(['name' => $department['name']], $department);
        }
    }
}
