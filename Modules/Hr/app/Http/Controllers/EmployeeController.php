<?php

namespace Modules\Hr\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Modules\Hr\Models\Employee;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Services\EmployeeService;
use Modules\Hr\Http\Requests\EmployeeRequest;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Enums\GenderEnum;
use Modules\Hr\Enums\MaritalStatusEnum;

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
            'employees'     => Employee::with('manager')->filter(request()->query() ?? [])->paginate(request('perPage', 10)),
            'managers'      => User::pluck('name', 'id'),
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
            'method_type'       => 'post',
            'action'            => route('hr.employees.store'),
            'genders'           => GenderEnum::items(),
            'marital_statuess'  => MaritalStatusEnum::items(),
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
     * Show the form for editing the given employee.
     *
     * @param  \Modules\Hr\Models\Employee  $employee
     * @return \Inertia\Response
     */
    public function edit(Employee $employee)
    {
        abort_if(Gate::denies('employee_create'), 403);

        return Inertia::render($this->path . 'EmployeeFormComponent', [
            'employee'      => $employee,
            'method_type'   => 'put',
            'action'        => route('hr.employees.update', $employee)
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
