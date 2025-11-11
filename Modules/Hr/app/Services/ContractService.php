<?php

namespace  Modules\Hr\Services;

use Modules\Hr\Models\Section;
use Modules\Hr\Models\Division;
use Modules\Hr\Models\Employee;
use Modules\Hr\Models\Position;
use Modules\Hr\Models\Department;
use Modules\Hr\Models\WorkSchedule;
use Modules\Hr\Models\AllowanceType;
use Modules\Hr\Models\ContractSalary;
use Modules\Hr\Models\TimeManagement;
use Modules\Hr\Models\EmployeeContract;
use Modules\Hr\Models\InsuranceCompany;
use Modules\Hr\Enums\AllowancesTypeEnum;

class ContractService
{
    /**
     * Store a newly created EmployeeContract in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\EmployeeContract
     */
    public function store(array $data): EmployeeContract
    {
        $contract = EmployeeContract::create($data['step_1'] ?? []);
        $contract->contractPositions()->create($data['step_1'] ?? []);
        $contract->salary()->create($data['step_2'] ?? []);
        $allowances = array_replace($data['step_2']['off_cycle_allowances'] ?? [], $data['step_2']['recurring_allowances'] ?? []);
        $contract->allowances()->createMany($this->optimizeAllowances($allowances));

        return $contract;
    }

    /**
     * Update the specified EmployeeContract in storage.
     *
     * @param  \Modules\Hr\Models\EmployeeContract  $contract
     * @param  array  $data
     * @return \Modules\Hr\Models\EmployeeContract
     */
    public function update(EmployeeContract $contract, array $data): EmployeeContract
    {
        $contract->updateOrCreate([
            'id'    => $contract->id
        ], $data['step_1']);

        $this->syncContract($contract, $data['step_1'], $contract);
        $this->syncSalary($contract, $data['step_2'], $contract?->salary ?? null);

        $allowances = array_replace($data['step_2']['off_cycle_allowances'] ?? [], $data['step_2']['recurring_allowances'] ?? []);
        $this->syncAllowances($contract, $allowances);

        return $contract;
    }

    /**
     * Remove the specified EmployeeContract from storage.
     *
     * @param  \Modules\Hr\Models\EmployeeContract  $contract
     * @return void
     */
    public function destroy(EmployeeContract $contract): void
    {
        $contract->delete();
    }

    /**
     * Returns an array of initialization data for the EmployeeContract form.
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
        $allowances = AllowanceType::all();
        return [
            'employee_id'           => request()->get('employee_id'),
            'redirect_url'          => request()->get('redirect_url'),
            'employees'             => Employee::orderBy('name')->pluck('name', 'id'),
            'divisions'             => Division::pluck('name', 'id'),
            'departments'           => Department::get(['id', 'name', 'division_id']),
            'sections'              => Section::get(['id', 'name', 'department_id']),
            'positions'             => Position::get(['id', 'name', 'section_id']),
            'allowances'            => $this->initilizeAllowances($allowances),
            'insurance_companies'   => InsuranceCompany::pluck('name', 'id'),
            'time_managements'      => TimeManagement::pluck('name', 'id'),
            'work_schedules'        => WorkSchedule::pluck('name', 'id'),
        ];
    }

    /**
     * Initializes an array of allowance types, separating them into
     * 'off_cycle' and 'recurring' categories.
     *
     * @param  AllowanceType  $allowances  An array of allowance types
     * @return array  An array of allowance types, separated into 'off_cycle' and 'recurring' categories
     */
    private function initilizeAllowances($allowances): array
    {
        return [
            'off_cycle'     => $allowances->where('type', AllowancesTypeEnum::OFF_CYCLE)->map(function ($allowance) {
                return [
                    'id'        => $allowance->id,
                    'name'      => $allowance->name,
                    'taxable'   => $allowance->taxable,
                ];
            }),
            'recurring'     => $allowances->where('type', AllowancesTypeEnum::RECURRING)->map(function ($allowance) {
                return [
                    'id'        => $allowance->id,
                    'name'      => $allowance->name,
                    'taxable'   => $allowance->taxable
                ];
            }),
        ];
    }

    /**
     * Optimizes an array of allowance types, converting it into an array
     * of objects containing the allowance type ID and amount.
     *
     * @param  array  $allowances  An array of allowance types, where the key is the
     *                                   allowance type ID and the value is the amount.
     * @return array  An array of objects containing the allowance type ID and amount.
     */
    private function optimizeAllowances(array $allowances): array
    {
        $resultAllowances = [];

        foreach ($allowances as $key => $allowance) {
            $resultAllowances[] = [
                'allowance_id' => $key,
                'amount'       => $allowance
            ];
        }
        return $resultAllowances;
    }

    /**
     * Synchronizes the contract data of an employee.
     *
     * If a contract object is provided, it will be updated with the given data.
     * If no contract object is provided, a new contract will be created with the given data.
     *
     * @param \Modules\Hr\Models\EmployeeContract $contract The employee to synchronize the contract for.
     * @param array $data The data to synchronize the contract with.
     */
    private function syncContract(EmployeeContract $contract, array $data)
    {
        $contract->update($data);
        $contract->contractPositions()->updateOrCreate([
            'contract_id'    => $contract->contract_id
        ], $data);
    }

    /**
     * Synchronizes the salary data of an employee contract.
     *
     * If a ContractSalary object is provided, it will be updated with the given data.
     * If no ContractSalary object is provided, a new ContractSalary will be created with the given data.
     *
     * @param \Modules\Hr\Models\EmployeeContract $contract The contract object to update the salary for.
     * @param array $data The data to synchronize the contract salary with.
     * @param \Modules\Hr\Models\ContractSalary|null $salary The ContractSalary object to update, or null to create a new one.
     */
    private function syncSalary(EmployeeContract $contract, array $data, ?ContractSalary $salary = null)
    {
        if ($salary) {
            $salary->update($data);
        } else {
            $contract->salary()->create($data);
        }
    }

    /**
     * Synchronizes the allowance data of an employee contract.
     *
     * Deletes all existing allowance records for the given contract and then
     * creates new allowance records for the given contract using the provided data.
     *
     * @param \Modules\Hr\Models\EmployeeContract $contract The contract object to synchronize the allowance data for.
     * @param array $data The data to synchronize the contract allowance data with.
     */
    public function syncAllowances(EmployeeContract $contract, array $data)
    {
        $contract->allowances()->delete();
        $contract->allowances()->createMany($this->optimizeAllowances($data));
    }

    /**
     * Changes the status of the given employee contract.
     *
     * @param \Modules\Hr\Models\EmployeeContract $contract The employee contract to change the status for.
     * @param int $status The new status of the employee contract.
     *
     * @return void
     */
    public function changeStatus(EmployeeContract $contract, int $status)
    {
        $contract->status = $status;
        $contract->save();
    }
}
