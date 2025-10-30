<?php

namespace Modules\UserManagement\Http\Controllers;

use Inertia\Inertia;
use Modules\UserManagement\Models\Permission;
use App\Http\Controllers\TransactionController;
use Modules\UserManagement\Services\PermissionService;
use Modules\UserManagement\Http\Requests\PermissionRequest;

class PermissionController extends TransactionController
{
    private $path = 'UserManagement::Permissions/';

    public function __construct(private PermissionService $roleService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render($this->path . 'PermissionsListComponent', [
            'permissions' => Permission::filter(request()->query() ?? [])->paginate(10),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionRequest $request)
    {
        return $this->withTransaction(function () use ($request) {
            $this->roleService->store($request->validated());
            return redirect()->route('user-management.permissions.index');
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        return $this->withTransaction(function () use ($request, $permission) {
            $this->roleService->update($permission, $request->validated());
            return redirect()->route('user-management.permissions.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        return $this->withTransaction(function () use ($permission) {
            $this->roleService->destroy($permission);
            return redirect()->route('user-management.permissions.index');
        });
    }
}
