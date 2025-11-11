<?php

namespace Modules\Hr\Http\Controllers;

use Inertia\Inertia;
use Modules\Hr\Models\Employee;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Services\EmployeeActivityService;

class EmployeeActivityController extends TransactionController
{
    private $path = 'Hr::Employees/Activity/';

    public function __construct(private EmployeeActivityService $employeeActivityService) {}

    /**
     * Render the bonuses list for the given employee.
     *
     * @param Employee $employee
     * @return \Inertia\InertiaResponse
     */
    public function bonuses(Employee $employee)
    {
        abort_if(Gate::denies('bonus_access'), 403);

        return Inertia::render($this->path . 'BonusesListComponent', [
            'employee' => $employee,
            ...$this->employeeActivityService->getBonusesData($employee),
        ]);
    }
    /**
     * Render the deductions list for the given employee.
     *
     * @param Employee $employee
     * @return \Inertia\InertiaResponse
     */
    public function deductions(Employee $employee)
    {
        abort_if(Gate::denies('deduction_access'), 403);

        return Inertia::render($this->path . 'DeductionsListComponent', [
            'employee' => $employee,
            ...$this->employeeActivityService->getDeductionsData($employee),
        ]);
    }

    /**
     * Render the contracts list for the given employee.
     *
     * @param Employee $employee
     * @return \Inertia\InertiaResponse
     */
    public function contracts(Employee $employee)
    {
        abort_if(Gate::denies('employee_contract_access'), 403);

        return Inertia::render($this->path . 'ContractsListComponent', [
            'employee' => $employee,
            ...$this->employeeActivityService->getContractsData($employee),
        ]);
    }
}
