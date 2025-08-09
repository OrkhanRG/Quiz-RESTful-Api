<?php

namespace App\Services;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Models\{
    Role,
    User,
    Permission
};
use App\Repositories\Contracts\RoleRepositoryInterface;

class RolePermissionService
{
    protected $roleRepository;
    protected $permissionRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        PermissionRepositoryInterface $permissionRepository
    )
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function getAllRoles()
    {
        return $this->roleRepository->getActiveRoles();
    }

    public function createRole(array $data): Role
    {
        return $this->roleRepository->create($data);
    }

    public function getRoleWithPermissions(int $roleId): Role
    {
        return $this->roleRepository->getRoleWithPermissions($roleId);
    }

    public function updateRole(int $roleId, array $data): Role
    {
        return $this->roleRepository->update($roleId, $data);
    }

    public function deleteRole(int $roleId): bool
    {
        $role = $this->roleRepository->find($roleId);

        if ($role->users()->count() > 0) {
            throw new \Exception('Bu rol istifadəçilərə təyin edilmişdir. Əvvəlcə rolları silin.');
        }

        return $this->roleRepository->delete($roleId);
    }

    public function assignPermissionsToRole(int $roleId, array $permissionIds): Role
    {
        $role = $this->roleRepository->find($roleId);
        $role->permissions()->sync($permissionIds);
        return $role->load('permissions');
    }

    public function removePermissionsFromRole(int $roleId, array $permissionIds): Role
    {
        $role = $this->roleRepository->find($roleId);
        $role->permissions()->detach($permissionIds);
        return $role->load('permissions');
    }

    public function assignRoleToUser(int $userId, string $roleName): User
    {
        $user = User::findOrFail($userId);
        $role = $this->roleRepository->getRoleByName($roleName);

        if (!$role) {
            throw new \Exception('Rol tapılmadı: ' . $roleName);
        }

        $user->roles()->syncWithoutDetaching([$role->id]);
        return $user->load('roles');
    }

    public function removeRoleFromUser(int $userId, string $roleName): User
    {
        $user = User::findOrFail($userId);
        $role = $this->roleRepository->getRoleByName($roleName);

        if (!$role) {
            throw new \Exception('Rol tapılmadı: ' . $roleName);
        }

        $user->roles()->detach($role->id);
        return $user->load('roles');
    }

    public function getRoleUsers(int $roleId)
    {
        return $this->roleRepository->getRoleUsers($roleId);
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

    public function hasPermission(int $userId, string $permission): bool
    {
        $userPermissions = $this->getUserPermissions($userId);
        return in_array($permission, $userPermissions);
    }

    public function getAllPermissions()
    {
        return $this->permissionRepository->all();
    }

    public function createPermission(array $data): Permission
    {
        return $this->permissionRepository->create($data);
    }

    public function getPermissionsByModule(string $module)
    {
        return $this->permissionRepository->getPermissionsByModule($module);
    }
}
