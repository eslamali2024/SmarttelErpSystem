<?php

namespace Modules\Hr\Database\Seeders;

use Modules\Hr\Models\Section;
use Illuminate\Database\Seeder;
use Modules\Hr\Models\Division;
use Modules\Hr\Models\Position;
use Modules\Hr\Models\Department;

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
                'division_id' => Division::get()->random()->id,
                'department_id' => Department::get()->random()->id,
                'section_id' => Section::get()->random()->id,
            ],
            [
                'name' => 'Supervisor',
                'code' => 'Supervisor',
                'division_id' => Division::get()->random()->id,
                'department_id' => Department::get()->random()->id,
                'section_id' => Section::get()->random()->id,
            ],
            [
                'name' => 'Staff',
                'code' => 'Staff',
                'division_id' => Division::get()->random()->id,
                'department_id' => Department::get()->random()->id,
                'section_id' => Section::get()->random()->id,
            ]
        ];

        foreach ($position as $position) {
            Position::updateOrCreate(['name' => $position['name']], $position);
        }
    }
}
