<?php

namespace Modules\Hr\Database\Seeders;

use Modules\Hr\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'name' => 'Support',
                'code' => 'support',
                'division_id' => 1,
                'department_id' => 1
            ],
        ];

        foreach ($sections as $section) {
            Section::updateOrCreate(['name' => $section['name']], $section);
        }
    }
}
