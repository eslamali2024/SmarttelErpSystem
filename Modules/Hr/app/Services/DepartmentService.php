<?php

namespace  Modules\Hr\Services;

use Modules\Hr\Models\Department;

class DepartmentService
{
    /**
     * Store a newly created Department in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\Department
     */
    public function store(array $data): Department
    {
        return Department::create($data);
    }

    /**
     * Update the specified Department in storage.
     *
     * @param  \Modules\Hr\Models\Department  $department
     * @param  array  $data
     * @return \Modules\Hr\Models\Department
     */
    public function update(Department $department, array $data): Department
    {
        $department->update($data);

        return $department;
    }

    /**
     * Remove the specified Department from storage.
     *
     * @param  \Modules\Hr\Models\Department  $department
     * @return void
     */
    public function destroy(Department $department): void
    {
        $department->delete();
    }
}
