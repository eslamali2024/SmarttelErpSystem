<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Models\TimeManagement;

class TimeManagmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name'             => 'Time Evaluation of Actual Times',
                'payroll'          => true,
                'fingerprint_in'   => true,
                'fingerprint_out'  => true,
                'factors'          => true,
            ],
            [
                'name'             => 'No Time Evaluation',
                'payroll'          => false,
                'fingerprint_in'   => false,
                'fingerprint_out'  => false,
                'factors'          => false,
            ],
            [
                'name'             => 'Time Evaluation Without Payroll Integration',
                'payroll'          => false,
                'fingerprint_in'   => true,
                'fingerprint_out'  => true,
                'factors'          => true,
            ],
            [
                'name'             => 'Time Evaluation of Planned Times',
                'payroll'          => true,
                'fingerprint_in'   => false,
                'fingerprint_out'  => false,
                'factors'          => true,
            ],
            [
                'name'             => 'Time Evaluation of Planned Times (In Only)',
                'payroll'          => true,
                'fingerprint_in'   => true,
                'fingerprint_out'  => false,
                'factors'          => true,
            ],
        ];

        foreach ($data as $item) {
            TimeManagement::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}
