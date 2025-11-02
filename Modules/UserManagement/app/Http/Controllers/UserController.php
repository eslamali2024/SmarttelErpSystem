<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Modules\UserManagement\Models\Role;
use Modules\UserManagement\Models\Permission;
use App\Http\Controllers\TransactionController;
use Modules\UserManagement\Services\RoleService;
use Modules\UserManagement\Services\UserService;
use Modules\UserManagement\Http\Requests\UserRequest;

class UserController extends TransactionController
{
    private $path = 'UserManagement::Users/';

    public function __construct(private UserService $userService, private RoleService $roleService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render($this->path . 'UsersListComponent', [
            'users' => User::with('roles')->filter(request()->query() ?? [])->paginate(request('perPage', 10)),
        ]);
    }


    /**
     * Returns a list of permissions for the create role form.
     *
     */
    public function create()
    {
        return response()->json([
            'roles'       => Role::pluck('name')->toArray(),
            'permissions' => $this->roleService->getParentPermissions()
        ]);
    }

    /**
     * Show the specified user.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json([
            'user'        => $user,
            'roles'       => $user?->roles()?->pluck('name')?->toArray() ?? [],
            'permissions' => $user?->permissions()?->pluck('name')?->toArray() ?? [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        return $this->withTransaction(function () use ($request, $user) {
            $this->userService->update($user, $request->validated());
            return redirect()->route('user-management.users.index', ['page' => request('page', 1)]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        return $this->withTransaction(function () use ($user) {
            $this->userService->destroy($user);
            return redirect()->route('user-management.users.index');
        });
    }
}
