<?php

namespace  Modules\UserManagement\Services;

use Modules\UserManagement\Models\Role;

class RoleService
{
    /**
     * Store a newly created Role in storage.
     *
     * @param  array  $data
     * @return Role
     */
    public function store(array $data): Role
    {
        $role = Role::create($data);
        $role->givePermissionTo($data['permissions'] ?? []);
        return $role;
    }

    /**
     * Update the specified Role in storage.
     *
     * @param  Role  $role
     * @param  array  $data
     * @return Role
     */
    public function update(Role $role, array $data): Role
    {
        $role->update($data);

        $role->syncPermissions($data['permissions'] ?? []);
        $role->forgetCachedPermissions();
        return $role;
    }

    /**
     * Remove the specified Role from storage.
     *
     * @param  Role  $role
     * @return bool
     */
    public function destroy(Role $role): bool
    {
        return $role->delete();
    }

    /**
     * Return an array of permissions with their respective groups
     *
     * @return array
     */
    public function getParentPermissions(): array
    {
        return config('permissions_detailed');
    }
}
