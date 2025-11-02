<?php

namespace  Modules\UserManagement\Services;

use App\Models\User;

class UserService
{
    /**
     * Update the specified User in storage.
     *
     * @param  User  $user
     * @param  array  $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        $user->syncRoles($data['roles'] ?? []);
        $user->syncPermissions($data['permissions'] ?? []);
        $user->forgetCachedPermissions();
        return $user;
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  User  $user
     * @return bool
     */
    public function destroy(User $user): bool
    {
        return $user->delete();
    }
}
