<?php

namespace  Modules\UserManagement\Services;

use Modules\UserManagement\Models\Permission;

class PermissionService
{
    /**
     * Store a newly created Permission in storage.
     *
     * @param  array  $data
     * @return Permission
     */
    public function store(array $data): Permission
    {
        return Permission::create($data);
    }

    /**
     * Update the specified Permission in storage.
     *
     * @param  Permission  $permission
     * @param  array  $data
     * @return Permission
     */
    public function update(Permission $permission, array $data): Permission
    {
        $permission->update($data);

        return $permission;
    }

    /**
     * Remove the specified Permission from storage.
     *
     * @param  Permission  $permission
     * @return void
     */
    public function destroy(Permission $permission): void
    {
        $permission->delete();
    }
}
