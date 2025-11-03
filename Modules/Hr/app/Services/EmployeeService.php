<?php

namespace  Modules\Hr\Services;

use Modules\Hr\Models\Employee;

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
        return Employee::create($data);
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
}
