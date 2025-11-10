<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Models\BonusType;

class BonusTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bonusTypes = [
            [
                'name'      => 'Incentive Bonus - مكافأة تحفيزية',
            ],

        ];

        foreach ($bonusTypes as $bonusType) {
            BonusType::updateOrCreate(['name' => $bonusType['name']], $bonusType);
        }
    }
}
