<?php

namespace  Modules\Hr\Services;

use Modules\Hr\Models\Section;
use Modules\Hr\Models\Division;
use Modules\Hr\Models\Employee;
use Modules\Hr\Models\Position;
use Modules\Hr\Enums\GenderEnum;
use Modules\Hr\Models\Department;
use Modules\Hr\Enums\MaritalStatusEnum;

class EmployeeService
{
    /**
     * Store a newly created Employee in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\Employee
     */
    public function store(array $data): Employee
    {
        $employee = Employee::create($data['step_1']);

        $contract = $employee->contracts()->create($data['step_2']);
        $contract->contractPositions()->create($data['step_2']);
        return $employee;
    }

    /**
     * Update the specified Employee in storage.
     *
     * @param  \Modules\Hr\Models\Employee  $employee
     * @param  array  $data
     * @return \Modules\Hr\Models\Employee
     */
    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);

        return $employee;
    }

    /**
     * Remove the specified Employee from storage.
     *
     * @param  \Modules\Hr\Models\Employee  $employee
     * @return void
     */
    public function destroy(Employee $employee): void
    {
        $employee->delete();
    }

    /**
     * Returns an array of initialization data for the Employee form.
     *
     * This function returns an array containing the following data:
     * - An array of genders
     * - An array of marital statuses
     * - An array of divisions
     * - An array of departments
     * - An array of sections
     * - An array of positions
     *
     * @return array
     */
    public function getInitilizeData(): array
    {
        return [
            'genders'            => GenderEnum::items(),
            'marital_statuess'   => MaritalStatusEnum::items(),
            'divisions'          => Division::pluck('name', 'id'),
            'departments'        => Department::get(['id', 'name', 'division_id']),
            'sections'           => Section::get(['id', 'name', 'department_id']),
            'positions'          => Position::get(['id', 'name', 'section_id']),
        ];
    }
}
