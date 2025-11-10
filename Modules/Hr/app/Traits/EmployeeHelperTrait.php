<?php

namespace Modules\Hr\Traits;

use Modules\Hr\Models\Section;
use Modules\Hr\Models\Division;
use Modules\Hr\Models\Employee;
use Modules\Hr\Models\Department;

trait EmployeeHelperTrait
{
    /**
     * Get employees list.
     *
     * If user is HR manager/staff, show all employees
     * If user is department manager, show their department employees
     * Regular employees can only see themselves
     *
     * @return \Illuminate\Support\Collection An associative array of employee IDs and names.
     */
    public function getEmployees()
    {
        $user = auth()->user();

        // HR/super-admin sees all
        if ($user->hasAnyRole('super-admin', 'hr-manager', 'hr-staff')) {
            return Employee::orderBy('name')->pluck('name', 'id');
        }

        // Handle non-employee users
        if (!$user->employee) {
            return collect();
        }

        $position = $user->employee?->current_company_position;
        if (!$position) {
            return collect([$user->employee->id => $user->employee->name]);
        }

        $hierarchy = [
            'section'       => Section::where('manager_id', $user->id)->get(),
            'department'    => Department::where('manager_id', $user->id)->get(),
            'division'      => Division::where('manager_id', $user->id)->get(),
        ];

        $managedLevels = collect($hierarchy)
            ->filter(fn($model) => $model && $model->isNotEmpty());

        if ($managedLevels->isNotEmpty()) {
            $managedLevel = $managedLevels?->keys()?->last();
            $managerId = $managedLevels?->last()?->pluck('id');
            $employees = Employee::whereHas('current_company_position', function ($query) use ($managedLevel, $managerId) {
                $query->where(function ($q) use ($managedLevel, $managerId) {
                    $q->whereIn($managedLevel . '_id', $managerId);
                });
            })
                ->groupBy('id')
                ->orderBy('name')
                ->pluck('name', 'id');

            // If user is not in the list, add them
            if (!in_array($user->employee?->id ?? null, $employees->keys()->toArray())) {
                $employees->put($user->employee?->id, $user->employee?->name);
            }
            return  $employees;
        }
        return collect([$user->employee?->id => $user->employee?->name]);
    }
}
