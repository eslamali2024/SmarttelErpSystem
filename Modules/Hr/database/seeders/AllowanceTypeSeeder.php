<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Models\AllowanceType;
use Modules\Hr\Enums\AllowancesTypeEnum;
use Modules\Hr\Enums\AllowancesTaxableEnum;

class AllowanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allowanceTypes = [
            [
                'name'      => 'Car allowance',
                'type'      => AllowancesTypeEnum::RECURRING,
                'taxable'   => AllowancesTaxableEnum::TAXABLE,
            ],
            [
                'name'      => 'Traveling allowance',
                'type'      => AllowancesTypeEnum::RECURRING,
                'taxable'   => AllowancesTaxableEnum::TAXABLE,
            ],
            [
                'name'      => 'Meal allowance',
                'type'      => AllowancesTypeEnum::RECURRING,
                'taxable'   => AllowancesTaxableEnum::TAXABLE,
            ],
            [
                'name'      => 'Transfer allowance',
                'type'      => AllowancesTypeEnum::RECURRING,
                'taxable'   => AllowancesTaxableEnum::TAXABLE,
            ],
            [
                'name'      => 'Housing allowance',
                'type'      => AllowancesTypeEnum::RECURRING,
                'taxable'   => AllowancesTaxableEnum::TAXABLE,
            ],
            [
                'name'      => 'First child allowance',
                'type'      => AllowancesTypeEnum::OFF_CYCLE,
                'taxable'   => AllowancesTaxableEnum::TAXABLE,
            ],
            [
                'name'      => 'Marriage allowance',
                'type'      => AllowancesTypeEnum::OFF_CYCLE,
                'taxable'   => AllowancesTaxableEnum::TAXABLE,
            ],
            [
                'name'      => 'Second child allowance',
                'type'      => AllowancesTypeEnum::OFF_CYCLE,
                'taxable'   => AllowancesTaxableEnum::TAXABLE,
            ],
        ];

        foreach ($allowanceTypes as $allowanceType) {
            AllowanceType::updateOrCreate(['name' => $allowanceType['name']], $allowanceType);
        }
    }
}
