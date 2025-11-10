<?php

namespace Modules\Hr\Services;

use Modules\Hr\Models\Deduction;
use Modules\Hr\Traits\EmployeeHelperTrait;

class DeductionService
{
    use EmployeeHelperTrait;

    /**
     * Store a newly created Deduction in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\Deduction
     */
    public function store(array $data): Deduction
    {
        return Deduction::create($data);
    }

    /**
     * Update the specified Deduction in storage.
     *
     * @param  \Modules\Hr\Models\Deduction  $deduction
     * @param  array  $data
     * @return \Modules\Hr\Models\Deduction
     */
    public function update(Deduction $deduction, array $data): Deduction
    {
        $deduction->update($data);

        return $deduction;
    }

    /**
     * Remove the specified Deduction from storage.
     *
     * @param  \Modules\Hr\Models\Deduction  $deduction
     * @return void
     */
    public function destroy(Deduction $deduction): void
    {
        $deduction->delete();
    }

    public function employees()
    {
        return $this->getEmployees();
    }
}
