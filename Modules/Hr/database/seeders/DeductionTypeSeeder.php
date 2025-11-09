<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Models\DeductionType;

class DeductionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deductionTypes = [
            [
                'name'      => 'خصم جدعني مني كدا',
            ],

        ];

        foreach ($deductionTypes as $deductionType) {
            DeductionType::updateOrCreate(['name' => $deductionType['name']], $deductionType);
        }
    }
}
