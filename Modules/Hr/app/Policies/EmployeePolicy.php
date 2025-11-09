<?php

namespace Modules\Hr\Policies;

use App\Models\User;
use Modules\Hr\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    /**
     * Check if the user has permission to edit the avatar of the given employee.
     *
     * @param User $user The user to check the permission for.
     * @param Employee $employee The employee to edit the avatar of.
     *
     * @return bool True if the user has permission, false otherwise.
     */
    public function employee_edit_avatar(User $user, Employee $employee)
    {
        return $user->id === $employee->user_id || auth()->user()?->hasRole(['super-admin', 'admin', 'hr-manager', 'hr-staff']);
    }
}
