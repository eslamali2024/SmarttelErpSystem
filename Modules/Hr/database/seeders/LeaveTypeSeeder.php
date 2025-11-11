<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Models\LeaveType;
use Modules\Hr\Enums\LeaveTypeUnitEnum;
use Modules\Hr\Enums\LeaveTypeDurationEnum;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name'                           => 'Annual Leave',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Casual Leave',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Sick Leave',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Sick Paid 75%',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => false,
                'duration_deducted_percentage'   => 25,
                'salary_deducted_percentage'     => 25,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Sick Paid 85%',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => false,
                'duration_deducted_percentage'   => 15,
                'salary_deducted_percentage'     => 15,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Sick Unpaid',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => false,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 100,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                              => 'Haj Leave',
                'unit'                              => LeaveTypeUnitEnum::DAYS,
                'for_employee'                      => true,
                'duration_deducted_percentage'      => 100,
                'salary_deducted_percentage'        => 0,
                'company_amount'                    => 30,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Marriage Leave',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'company_amount'                 => 7,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Maternity Leave',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => '1/2 Day Morning',
                'unit'                           => LeaveTypeUnitEnum::HOURS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 50,
                'salary_deducted_percentage'     => 50,
                'type_duration'                  => LeaveTypeDurationEnum::STATIC_HOUR_HALF_MORNING->value
            ],
            [
                'name'                           => '1/2 Day Evening',
                'unit'                           => LeaveTypeUnitEnum::HOURS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 50,
                'salary_deducted_percentage'     => 50,
                'type_duration'                  => LeaveTypeDurationEnum::STATIC_HOUR_HALF_NIGHT->value
            ],
            [
                'name'                            => 'Unpaid',
                'unit'                            => LeaveTypeUnitEnum::DAYS,
                'for_employee'                    => true,
                'duration_deducted_percentage'    => 100,
                'salary_deducted_percentage'      => 100,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Unpaid 1/2 Day',
                'unit'                           => LeaveTypeUnitEnum::HOURS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 50,
                'salary_deducted_percentage'     => 50,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Paid Leave',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => false,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'company_amount'                 => 10,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Special Excuse',
                'unit'                           => LeaveTypeUnitEnum::HOURS,
                'for_employee'                   => false,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Day Off Compensation',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => false,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Time in lieu',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => false,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Birth Leave',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => false,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'company_amount'                 => 1,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Death Leave',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Education Leave days',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => false,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'company_amount'                 => 10,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Education Leave hours',
                'unit'                           => LeaveTypeUnitEnum::HOURS,
                'for_employee'                   => false,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'company_amount'                 => 10,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Feeding Excuse',
                'unit'                           => LeaveTypeUnitEnum::HOURS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Pregnancy Excuse',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ],
            [
                'name'                           => 'Military Service Leave',
                'unit'                           => LeaveTypeUnitEnum::DAYS,
                'for_employee'                   => true,
                'duration_deducted_percentage'   => 100,
                'salary_deducted_percentage'     => 0,
                'type_duration'                  => LeaveTypeDurationEnum::FIXED->value
            ]
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }
    }
}
