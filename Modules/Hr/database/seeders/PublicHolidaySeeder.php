<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Models\PublicHoliday;

class PublicHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publicHolidays = [
            // [
            //     'name'                 => '',
            //     'start_date'           => '',
            //     'end_date'             => '',
            //     'actual_start_date'    => '',
            //     'actual_end_date'      => '',
            //     'days'                 => '',
            // ],

        ];

        foreach ($publicHolidays as $publicHoliday) {
            PublicHoliday::updateOrCreate(['name' => $publicHoliday['name']], $publicHoliday);
        }
    }
}
