<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Models\Department;
use Modules\Hr\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $position = [
            [
                'name' => 'Manager',
                'code' => 'Manager',
                'department_id' => Department::get()->random()->id,
            ],
            [
                'name' => 'Supervisor',
                'code' => 'Supervisor',
                'department_id' => Department::get()->random()->id,
            ],
            [
                'name' => 'Staff',
                'code' => 'Staff',
                'department_id' => Department::get()->random()->id,
            ]
        ];

        foreach ($position as $position) {
            Position::updateOrCreate(['name' => $position['name']], $position);
        }
    }
}
