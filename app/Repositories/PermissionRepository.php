<?php

namespace App\Repositories;

use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Models\Permission;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    public function __construct(Permission $permission)
    {
        parent::__construct($permission);
    }

    public function getPermissionsByModule(string $module)
    {
        return $this->model->where('module', $module)
            ->where('status', '1')
            ->orderBy('name')
            ->get();
    }

    public function getActivePermissions()
    {
        return $this->model->where('status', '1')
            ->orderBy('module')
            ->orderBy('name')
            ->get();
    }

    public function getPermissionByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }
}
