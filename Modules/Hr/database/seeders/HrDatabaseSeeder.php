<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Database\Seeders\PositionSeeder;
use Modules\Hr\Database\Seeders\DepartmentSeeder;

class HrDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            PositionSeeder::class
        ]);
    }
}
