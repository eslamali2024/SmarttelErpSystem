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
            DivisionSeeder::class,
            DepartmentSeeder::class,
            SectionSeeder::class,
            PositionSeeder::class,
            InsuranceCompanySeeder::class,
            AllowanceTypeSeeder::class,
            TimeManagmentSeeder::class,
            WorkScheduleSeeder::class,
            PublicHolidaySeeder::class,
            BonusTypeSeeder::class,
            DeductionTypeSeeder::class,
            LeaveTypeSeeder::class
        ]);
    }
}
