<?php

namespace Modules\Hr\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Hr\Models\InsuranceCompany;

class InsuranceCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insuranceCompanys = [
            [
                'name'      => 'Axa',
            ],
        ];

        foreach ($insuranceCompanys as $insuranceCompany) {
            InsuranceCompany::updateOrCreate(['name' => $insuranceCompany['name']], $insuranceCompany);
        }
    }
}
