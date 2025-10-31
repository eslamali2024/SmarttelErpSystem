<?php

namespace Modules\UserManagement\Http\Controllers;

use Inertia\Inertia;
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
        return Inertia::render($this->path . 'RolesListComponent', [
            'roles' => Role::filter(request()->query() ?? [])->paginate(10),
        ]);
    }

    /**
     * Returns a list of permissions for the create role form.
     *
     */
    public function create()
    {
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
        return $this->withTransaction(function () use ($request) {
            $this->roleService->store($request->validated());
            return redirect()->route('user-management.roles.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role)
    {
        return $this->withTransaction(function () use ($request, $role) {
            $this->roleService->update($role, $request->validated());
            return redirect()->route('user-management.roles.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        return $this->withTransaction(function () use ($role) {
            $this->roleService->destroy($role);
            return redirect()->route('user-management.roles.index');
        });
    }
}
