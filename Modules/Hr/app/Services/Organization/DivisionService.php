<?php

namespace Modules\Hr\Services\Organization;

use Modules\Hr\Models\Division;

class DivisionService
{
    /**
     * Store a newly created Division in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\Division
     */
    public function store(array $data): Division
    {
        return Division::create($data);
    }

    /**
     * Update the specified Division in storage.
     *
     * @param  \Modules\Hr\Models\Division  $division
     * @param  array  $data
     * @return \Modules\Hr\Models\Division
     */
    public function update(Division $division, array $data): Division
    {
        $division->update($data);

        return $division;
    }

    /**
     * Remove the specified Division from storage.
     *
     * @param  \Modules\Hr\Models\Division  $division
     * @return void
     */
    public function destroy(Division $division): void
    {
        $division->delete();
    }
}
