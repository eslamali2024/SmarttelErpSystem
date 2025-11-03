<?php

namespace Modules\UserManagement\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use Modules\UserManagement\Models\Role;
use App\Http\Controllers\TransactionController;
use Modules\UserManagement\Services\RoleService;
use Modules\UserManagement\Http\Requests\RoleRequest;

class RoleController extends TransactionController
{
    private $path = 'UserManagement::Roles/';

    public function __construct(private RoleService $roleService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('role_access'), 403);

        return Inertia::render($this->path . 'RolesListComponent', [
            'roles' => Role::filter(request()->query() ?? [])->paginate(request('perPage', 10)),
        ]);
    }

    /**
     * Returns a list of permissions for the create role form.
     *
     */
    public function create()
    {
        abort_if(Gate::denies('role_create'), 403);

        return response()->json([
            $this->roleService->getParentPermissions()
        ]);
    }

    /**
     * Show the specified role.
     *
     * @param  Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        abort_if(Gate::denies('role_show'), 403);

        return response()->json([
            'role'        => $role,
            'permissions' => $role?->permissions()?->pluck('name')?->toArray() ?? []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        abort_if(Gate::denies('role_create'), 403);

        return $this->withTransaction(function () use ($request) {
            $this->roleService->store($request->validated());
            return redirect()->route('user-management.roles.index', ['page' => request('page', 1)]);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role)
    {
        abort_if(Gate::denies('role_edit'), 403);

        return $this->withTransaction(function () use ($request, $role) {
            $this->roleService->update($role, $request->validated());
            return redirect()->route('user-management.roles.index', ['page' => request('page', 1)]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        abort_if(Gate::denies('role_delete'), 403);

        return $this->withTransaction(function () use ($role) {
            $this->roleService->destroy($role);
            return redirect()->route('user-management.roles.index');
        });
    }
}
