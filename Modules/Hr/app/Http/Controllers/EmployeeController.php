<?php

namespace Modules\Hr\Http\Controllers;

use Inertia\Inertia;
use GuzzleHttp\Psr7\Request;
use Modules\Hr\Models\Employee;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Services\EmployeeService;
use Modules\Hr\Http\Requests\EmployeeRequest;
use Modules\Hr\Transformers\EmployeeFormResource;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Enums\ContractStatusEnum;
use Modules\Hr\Enums\GenderEnum;
use Modules\Hr\Http\Requests\EmployeeUpdateAvaterRequest;
use Modules\Hr\Http\Requests\GrossUpSalaryRequest;
use Modules\Hr\Transformers\EmployeeResource;

class EmployeeController extends TransactionController
{
    private $path = 'Hr::Employees/';

    public function __construct(private EmployeeService $employeeService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('employee_access'), 403);

        return Inertia::render($this->path . 'EmployeesListComponent', [
            'employees'     =>  Employee::filter(request()->query() ?? [])->paginate(request('perPage', 10)),
            'statuses'      =>  ContractStatusEnum::items(),
            'genders'       =>  GenderEnum::items()
        ]);
    }

    /**
     * Create a new employee.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        abort_if(Gate::denies('employee_create'), 403);

        return Inertia::render($this->path . 'EmployeeFormComponent', [
            'method_type'        => 'post',
            'action'             => route('hr.employees.store'),
            ...$this->employeeService->getInitilizeData()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
        abort_if(Gate::denies('employee_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->employeeService->store($request->validated());
            return redirect()->route('hr.employees.index');
        });
    }

    /**
     * Show the details of the given employee.
     *
     * @param  \Modules\Hr\Models\Employee  $employee
     * @return \Inertia\Response
     */
    public function show(Employee $employee)
    {
        abort_if(Gate::denies('employee_show'), 403);

        return Inertia::render($this->path . 'EmployeeShowComponent', [
            'employee'              => new EmployeeResource($employee),
            'canEditAvataer'        => Gate::check('employee_edit_avatar', $employee),
        ]);
    }

    /**
     * Returns the gross salary of an employee given their net salary.
     *
     * @param GrossUpSalaryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function getGrossUp(GrossUpSalaryRequest $request)
    {
        return response()->json(
            $this->employeeService->getGrossUp($request->validated()['net_salary'] ?? 0)
        );
    }

    /**
     * Show the form for editing the given employee.
     *
     * @param  \Modules\Hr\Models\Employee  $employee
     * @return \Inertia\Response
     */
    public function edit(Employee $employee)
    {
        abort_if(Gate::denies('employee_create'), 403);
        $employee->load([
            'currentContract.currentPosition'
        ]);

        return Inertia::render($this->path . 'EmployeeFormComponent', [
            'item'               => new EmployeeFormResource($employee),
            'method_type'        => 'put',
            'action'             => route('hr.employees.update', $employee),
            ...$this->employeeService->getInitilizeData()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        abort_if(Gate::denies('employee_edit'), 403);

        return $this->withTransaction(function () use ($request, $employee) {
            $this->employeeService->update($employee, $request->validated());
            return redirect()->route('hr.employees.index');
        });
    }

    /**
     * Update the avatar of the given employee.
     *
     * @param  \Illuminate\Http\EmployeeUpdateAvaterRequest  $request
     * @param  \Modules\Hr\Models\Employee  $employee
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAvatar(EmployeeUpdateAvaterRequest $request, Employee $employee)
    {
        abort_if(Gate::denies('employee_edit_avatar', $employee), 403);

        return $this->withTransaction(function () use ($request, $employee) {
            $this->employeeService->updateAvatar($employee, $request->validated());
            return redirect()->route('hr.employees.show', $employee);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        abort_if(Gate::denies('employee_delete'), 403);

        return $this->withTransaction(function () use ($employee) {
            $this->employeeService->destroy($employee);
            return redirect()->route('hr.employees.index');
        });
    }
}
