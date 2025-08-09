<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@kidia.com',
            'password' => Hash::make('password'),
            'status' => UserStatus::ACTIVE
        ]);
        $superAdmin->assignRole('super_admin');

        $admin = User::create([
            'name' => 'Admin İstifadəçi',
            'email' => 'admin@kidia.com',
            'password' => Hash::make('password'),
            'status' => UserStatus::ACTIVE
        ]);
        $admin->assignRole('admin');

        $teacher = User::create([
            'name' => 'Müəllim 1',
            'email' => 'teacher@kidia.com',
            'password' => Hash::make('password'),
            'status' => UserStatus::ACTIVE
        ]);
        $teacher->assignRole('teacher');

        $student1 = User::create([
            'name' => 'Şagird 1',
            'email' => 'student1@kidia.com',
            'password' => Hash::make('password'),
            'status' => UserStatus::ACTIVE
        ]);
        $student1->assignRole('student');

        $student2 = User::create([
            'name' => 'Şagird 2',
            'email' => 'student2@kidia.com',
            'password' => Hash::make('password'),
            'status' => UserStatus::ACTIVE
        ]);
        $student2->assignRole('student');
    }
}

