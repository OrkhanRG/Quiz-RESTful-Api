<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email);
    public function getActiveUsers();
    public function getUsersByRole(string $roleName);
    public function getUserWithRoles(int $userId);
    public function getUserStatistics(int $userId);
    public function searchUsers(string $search);
    public function getTeachers();
    public function getStudents();
}
