<?php

namespace Modules\Hr\Http\Controllers\Organization;

use App\Models\User;
use Inertia\Inertia;
use Modules\Hr\Models\Department;
use App\Http\Controllers\TransactionController;
use Modules\Hr\Services\DepartmentService;
use Modules\Hr\Http\Requests\DepartmentRequest;
use Modules\Hr\Models\Division;

class DepartmentController extends TransactionController
{
    private $path = 'Hr::Organization/Departments/';

    public function __construct(private DepartmentService $departmentService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        return $this->withTransaction(function () use ($department) {
            $this->departmentService->destroy($department);
            return redirect()->route('hr.organization.departments.index');
        });
    }
}
