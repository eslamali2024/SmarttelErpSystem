<?php

namespace Modules\Hr\Http\Controllers\Organization;

use App\Models\User;
use Inertia\Inertia;
use Modules\Hr\Models\Division;
use Modules\Hr\Models\Department;
use Illuminate\Support\Facades\Gate;
use Modules\Hr\Services\DepartmentService;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Http\Requests\DepartmentRequest;

class DepartmentController extends TransactionController
{
    private $path = 'Hr::Organization/Departments/';

    public function __construct(private DepartmentService $departmentService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('department_access'), 403);

        return Inertia::render($this->path . 'DepartmentsListComponent', [
            'departments' => Department::with(['manager', 'division'])->filter(request()->query() ?? [])->paginate(10),
            'divisions'   => Division::pluck('name', 'id'),
            'managers'    => User::pluck('name', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentRequest $request)
    {
        abort_if(Gate::denies('department_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->departmentService->store($request->validated());
            return redirect()->route('hr.organization.departments.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentRequest $request, Department $department)
    {
        abort_if(Gate::denies('department_edit'), 403);

        return $this->withTransaction(function () use ($request, $department) {
            $this->departmentService->update($department, $request->validated());
            return redirect()->route('hr.organization.departments.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        abort_if(Gate::denies('department_delete'), 403);

        return $this->withTransaction(function () use ($department) {
            $this->departmentService->destroy($department);
            return redirect()->route('hr.organization.departments.index');
        });
    }
}
