<?php

namespace Modules\Hr\Services;

use Modules\Hr\Models\Bonus;
use Modules\Hr\Traits\EmployeeHelperTrait;

class BonusService
{
    use EmployeeHelperTrait;

    /**
     * Store a newly created Bonus in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\Bonus
     */
    public function store(array $data): Bonus
    {
        return Bonus::create($data);
    }

    /**
     * Update the specified Bonus in storage.
     *
     * @param  \Modules\Hr\Models\Bonus  $bonus
     * @param  array  $data
     * @return \Modules\Hr\Models\Bonus
     */
    public function update(Bonus $bonus, array $data): Bonus
    {
        $bonus->update($data);

        return $bonus;
    }

    /**
     * Remove the specified Bonus from storage.
     *
     * @param  \Modules\Hr\Models\Bonus  $bonus
     * @return void
     */
    public function destroy(Bonus $bonus): void
    {
        $bonus->delete();
    }

    public function employees()
    {
        return $this->getEmployees();
    }
}
