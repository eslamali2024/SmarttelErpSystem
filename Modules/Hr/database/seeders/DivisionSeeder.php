<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Models\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            [
                'name' => 'Support',
                'code' => 'support',
                'manager_id' => 1
            ],
            [
                'name' => 'Engineering',
                'code' => 'engineering',
                'manager_id' => 1
            ]
        ];

        foreach ($divisions as $division) {
            Division::updateOrCreate(['name' => $division['name']], $division);
        }
    }
}
