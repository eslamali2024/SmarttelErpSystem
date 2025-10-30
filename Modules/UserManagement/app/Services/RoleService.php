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
        return Role::create($data);
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
}
