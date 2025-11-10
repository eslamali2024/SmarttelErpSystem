<?php

namespace  Modules\Hr\Services;

use App\Models\User;
use Modules\Hr\Models\Section;
use Modules\Hr\Models\Division;
use Modules\Hr\Models\Employee;
use Modules\Hr\Models\Position;
use Modules\Hr\Enums\GenderEnum;
use Modules\Hr\Models\Department;
use Modules\Hr\Models\WorkSchedule;
use Modules\Hr\Traits\GrossUpTrait;
use Illuminate\Support\Facades\Hash;
use Modules\Hr\Models\AllowanceType;
use Modules\Hr\Models\ContractSalary;
use Modules\Hr\Models\TimeManagement;
use Modules\Hr\Enums\MaritalStatusEnum;
use Modules\Hr\Models\EmployeeContract;
use Modules\Hr\Models\InsuranceCompany;
use Modules\Hr\Enums\AllowancesTypeEnum;
use Modules\Hr\Traits\CalculateIcomeTax;

class EmployeeService
{
    use GrossUpTrait;

    /**
     * Store a newly created Employee in storage.
     *
     * @param  array  $data
     * @return \Modules\Hr\Models\Employee
     */
    public function store(array $data): Employee
    {
        $employee = Employee::create($data['step_1']);

        User::create([
            'name'      => $data['step_1']['name'] ?? '',
            'email'     => $data['step_1']['email'] ?? '',
            'password'  => Hash::make('123456789'),
        ]);

        $contract = $employee->contracts()->create($data['step_2'] ?? []);
        $contract->contractPositions()->create($data['step_2'] ?? []);
        $contract->salary()->create($data['step_3'] ?? []);
        $allowances = array_replace($data['step_3']['off_cycle_allowances'] ?? [], $data['step_3']['recurring_allowances'] ?? []);
        $contract->allowances()->createMany($this->optimizeAllowances($allowances));

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
        $employee->update($data['step_1']);

        $contract = $employee->currentContract;
        $contract->updateOrCreate([
            'id'    => $contract->id
        ], $data['step_2']);

        $this->syncContract($employee, $data['step_2'], $contract);
        $this->syncSalary($contract, $data['step_3'], $contract?->salary ?? null);

        $allowances = array_replace($data['step_3']['off_cycle_allowances'] ?? [], $data['step_3']['recurring_allowances'] ?? []);
        $this->syncAllowances($contract, $allowances);

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
        $allowances = AllowanceType::all();
        return [
            'auto_generate_code'    => Employee::autoGenerateCode(),
            'genders'               => GenderEnum::items(),
            'marital_statuess'      => MaritalStatusEnum::items(),
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
     * Calculates the gross salary of an employee, given their net salary.
     * The algorithm starts with an initial guess of the gross salary being the target net salary divided by 0.75 (25% above the target net salary).
     * It then iterates up to 100 times, adjusting the gross salary based on the difference between the target net salary and the calculated net salary.
     * The adjustment factor is 1 - 0.25, meaning that the gross salary is adjusted by 75% of the difference.
     * If the difference between the target net salary and the calculated net salary is within 0.01 of the target net salary, the iteration stops.
     * If the maximum number of iterations is reached without finding a suitable gross salary, an error message is printed.
     *
     * @param float $net_salary The target net salary of the employee.
     *
     * @return array An array containing the calculated gross salary, net salary, tax, insurance, and deductions monthly.
     */
    public function getGrossUp(float $net_salary): array
    {
        $this->setCalculateIncomeTax(new CalculateIcomeTax());

        return $this->gross_up_salary($net_salary);
    }

    /**
     * Synchronizes the contract data of an employee.
     *
     * If a contract object is provided, it will be updated with the given data.
     * If no contract object is provided, a new contract will be created with the given data.
     *
     * @param \Modules\Hr\Models\Employee $employee The employee to synchronize the contract for.
     * @param array $data The data to synchronize the contract with.
     * @param \Modules\Hr\Models\EmployeeContract|null $contract The contract object to update, or null to create a new one.
     */
    private function syncContract(Employee $employee, array $data, ?EmployeeContract $contract = null)
    {
        if ($contract) {
            $contract->update($data);
        } else {
            $employee->contracts()->create($data);
        }
        $contract->currentPosition()->updateOrCreate([
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
     * Updates the avatar of the given employee.
     *
     * @param \Modules\Hr\Models\Employee $employee The employee to update the avatar for.
     * @param array $data The data to update the avatar with. The array should contain a single key 'avatar' with the value of the new avatar.
     */
    public function updateAvatar(Employee $employee, array $data)
    {
        $employee->clearMediaCollection('avatar');
        $employee->addMedia($data['avatar'] ?? null)->toMediaCollection('avatar');
    }
}
