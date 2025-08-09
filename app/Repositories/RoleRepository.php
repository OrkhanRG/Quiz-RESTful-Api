<?php

namespace App\Repositories;

use App\Enums\UserStatus;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Models\Role;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $role)
    {
        parent::__construct($role);
    }

    public function getRoleWithPermissions(int $roleId)
    {
        return $this->model->with(['permissions'])->findOrFail($roleId);
    }

    public function getRoleByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }

    public function getActiveRoles()
    {
        return $this->model->where('status', '1')
            ->with(['permissions'])
            ->get();
    }

    public function getRoleUsers(int $roleId)
    {
        return $this->model->findOrFail($roleId)
            ->users()
            ->where('status', UserStatus::ACTIVE)
            ->get();
    }
}
