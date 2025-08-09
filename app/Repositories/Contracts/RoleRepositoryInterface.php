<?php

namespace App\Repositories\Contracts;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function getRoleWithPermissions(int $roleId);
    public function getRoleByName(string $name);
    public function getActiveRoles();
    public function getRoleUsers(int $roleId);
}
