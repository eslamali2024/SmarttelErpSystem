<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Models\WorkSchedule;
use Modules\Hr\Enums\DaysInWeekEnum;
use Modules\Hr\Enums\WorkScheduleHolidayStatusEnum;

class WorkScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workSchedules = [
            [
                'name'                  => 'Office Based',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Office Based Managers',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Managers',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Site Projects Based Casual',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Site Projects Based',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Admin Maintenance',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Night Shift',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Spider',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Ramadan',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Ramadan Project-based',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Ramadan Office Based Managers',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Barista',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
            [
                'name'                  => 'Internship',
                'start_time'            => '08:00',
                'end_time'              => '17:00',
                'allow_late_sign_in'    => "08:15",
                'allow_early_sign_out'   => "16:45"
            ],
        ];

        foreach ($workSchedules as $workSchedule) {
            $workSchedule =    WorkSchedule::create($workSchedule);
            foreach (DaysInWeekEnum::getDays() as $day) {
                $workSchedule->days()->create([
                    'day'       => $day,
                    'status'    => WorkScheduleHolidayStatusEnum::WORKING
                ]);
            }
        }
    }
}
