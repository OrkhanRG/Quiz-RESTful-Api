<?php

namespace App\Repositories\Contracts;

interface PermissionRepositoryInterface extends BaseRepositoryInterface
{
    public function getPermissionsByModule(string $module);
    public function getActivePermissions();
    public function getPermissionByName(string $name);
}
