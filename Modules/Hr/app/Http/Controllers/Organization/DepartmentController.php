<?php

namespace Modules\Hr\Http\Controllers\Organization;

use App\Models\User;
use Inertia\Inertia;
use Modules\Hr\Models\Department;
use App\Http\Controllers\Controller;
use Modules\Hr\Http\Requests\DepartmentRequest;

class DepartmentController extends Controller
{
    private $path = 'Hr::Organization/Departments/';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query();
        unset($search['page']);

        return Inertia::render($this->path . 'DepartmentsListComponent', [
            'departments' => Department::with('manager')->filter($search ?? [])->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render($this->path . 'DepartmentFormComponent', [
            'method_type' => 'post',
            'action'      => route('hr.organization.departments.store'),
            'managers'    => User::pluck('name', 'id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DepartmentRequest $request)
    {
        Department::create($request->validated());

        return redirect()->route('hr.organization.departments.index');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('hr::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return Inertia::render($this->path . 'DepartmentFormComponent', [
            'method_type' => 'put',
            'action' => route('hr.organization.departments.update', $department->id),
            'department' => $department,
            'managers'    => User::pluck('name', 'id'),

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DepartmentRequest $request, Department $department)
    {
        $department->update($request->validated());
        return redirect()->route('hr.organization.departments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('hr.organization.departments.index');
    }
}
