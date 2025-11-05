<?php

namespace Modules\Hr\Services\MasterData;

use Modules\Hr\Models\InsuranceCompany;

class InsuranceCompanyService
{
    /**
     * Store a newly created InsuranceCompany in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\InsuranceCompany
     */
    public function store(array $data): InsuranceCompany
    {
        return InsuranceCompany::create($data);
    }

    /**
     * Update the specified InsuranceCompany in storage.
     *
     * @param  \Modules\Hr\Models\InsuranceCompany  $insuranceCompany
     * @param  array  $data
     * @return \Modules\Hr\Models\InsuranceCompany
     */
    public function update(InsuranceCompany $insuranceCompany, array $data): InsuranceCompany
    {
        $insuranceCompany->update($data);

        return $insuranceCompany;
    }

    /**
     * Remove the specified InsuranceCompany from storage.
     *
     * @param  \Modules\Hr\Models\InsuranceCompany  $insuranceCompany
     * @return void
     */
    public function destroy(InsuranceCompany $insuranceCompany): void
    {
        $insuranceCompany->delete();
    }
}
