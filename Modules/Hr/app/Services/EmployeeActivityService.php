<?php

namespace  Modules\Hr\Services;

use Modules\Hr\Models\Employee;
use App\Enums\Approval\ApprovalStatusEnum;
use Modules\Hr\Enums\ContractStatusEnum;

class EmployeeActivityService
{
    /**
     * Get bonuses data for the given employee.
     *
     * @param Employee $employee
     * @return array
     */
    public function getBonusesData(Employee $employee): array
    {
        return [
            'bonuses'    => $employee->bonuses()->with([
                'bonusType:id,name',
                'createdBy:id,name',
            ])->filter(request()->query() ?? [])->paginate(request('perPage', 10)),
            'statuses'   => ApprovalStatusEnum::items(),
            'redirect_url' => route('hr.employees.activity.bonuses', $employee->id),
        ];
    }

    /**
     * Get deductions data for the given employee.
     *
     * @param Employee $employee
     * @return array
     *
     * @return array
     */
    public function getDeductionsData(Employee $employee): array
    {
        return [
            'deductions'    => $employee->deductions()->with([
                'deductionType:id,name',
                'createdBy:id,name',
            ])->filter(request()->query() ?? [])->paginate(request('perPage', 10)),
            'statuses'   => ApprovalStatusEnum::items(),
            'redirect_url' => route('hr.employees.activity.deductions', $employee->id),
        ];
    }

    /**
     * Get contracts data for the given employee.
     *
     * @param Employee $employee
     *
     * @return array
     *
     * @return array
     */
    public function getContractsData(Employee $employee): array
    {
        return [
            'contracts'    => $employee->contracts()->with([
                'timeManagement',
                'workSchedule',
                'currentPosition',
                'currentPosition.position',
                'salary',
            ])->filter(request()->query() ?? [])->paginate(request('perPage', 10)),
            'statuses'   => ContractStatusEnum::items(),
            'redirect_url' => route('hr.employees.activity.contracts', $employee->id),
        ];
    }
}
