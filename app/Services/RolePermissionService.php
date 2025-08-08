<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;

class RolePermissionService
{
    public function createRole(array $data): Role
    {
        return Role::create($data);
    }

    public function assignPermissionsToRole(int $roleId, array $permissionIds): Role
    {
        $role = Role::findOrFail($roleId);
        $role->permissions()->sync($permissionIds);
        return $role;
    }

    public function assignRoleToUser(int $userId, string $roleName): User
    {
        $user = User::findOrFail($userId);
        return $user->assignRole($roleName);
    }

    public function getUserPermissions(int $userId): array
    {
        $user = User::with('roles.permissions')->findOrFail($userId);
        $permissions = [];

        foreach ($user->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission->name;
            }
        }

        return array_unique($permissions);
    }
}
