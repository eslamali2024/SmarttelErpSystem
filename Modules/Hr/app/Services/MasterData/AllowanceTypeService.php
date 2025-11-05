<?php

namespace Modules\Hr\Services\MasterData;

use Modules\Hr\Models\AllowanceType;

class AllowanceTypeService
{
    /**
     * Store a newly created AllowanceType in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\AllowanceType
     */
    public function store(array $data): AllowanceType
    {
        return AllowanceType::create($data);
    }

    /**
     * Update the specified AllowanceType in storage.
     *
     * @param  \Modules\Hr\Models\AllowanceType  $allowanceType
     * @param  array  $data
     * @return \Modules\Hr\Models\AllowanceType
     */
    public function update(AllowanceType $allowanceType, array $data): AllowanceType
    {
        $allowanceType->update($data);

        return $allowanceType;
    }

    /**
     * Remove the specified AllowanceType from storage.
     *
     * @param  \Modules\Hr\Models\AllowanceType  $allowanceType
     * @return void
     */
    public function destroy(AllowanceType $allowanceType): void
    {
        $allowanceType->delete();
    }
}
