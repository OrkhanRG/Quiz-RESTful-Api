<?php

namespace Database\Seeders;

use App\Models\{
    Permission,
    Role
};

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $superAdmin = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Administrator',
            'description' => 'Sistemdə tam səlahiyyətə malikdir'
        ]);

        $admin = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Sistem administratoru'
        ]);

        $teacher = Role::create([
            'name' => 'teacher',
            'display_name' => 'Müəllim',
            'description' => 'Test və sual yaradan müəllim'
        ]);

        $student = Role::create([
            'name' => 'student',
            'display_name' => 'Şagird',
            'description' => 'Test verən şagird'
        ]);

        $superAdmin->permissions()->attach(Permission::all());

        $adminPermissions = Permission::whereIn('module', ['user', 'role', 'permission', 'category'])->get();
        $admin->permissions()->attach($adminPermissions);

        $teacherPermissions = Permission::whereIn('name', [
            'quiz.view', 'quiz.create', 'quiz.update', 'quiz.delete', 'quiz.publish', 'quiz.view-attempts',
            'question.view', 'question.create', 'question.update', 'question.delete',
            'category.view'
        ])->get();
        $teacher->permissions()->attach($teacherPermissions);

        $studentPermissions = Permission::whereIn('name', [
            'quiz.view', 'quiz.take', 'category.view'
        ])->get();
        $student->permissions()->attach($studentPermissions);
    }
}
