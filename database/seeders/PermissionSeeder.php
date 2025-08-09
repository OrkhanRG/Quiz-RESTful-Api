<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Quiz permissions
            ['name' => 'quiz.view', 'display_name' => 'Testləri görmək', 'module' => 'quiz'],
            ['name' => 'quiz.view-all', 'display_name' => 'Bütün testləri görmək', 'module' => 'quiz'],
            ['name' => 'quiz.create', 'display_name' => 'Test yaratmaq', 'module' => 'quiz'],
            ['name' => 'quiz.update', 'display_name' => 'Test redaktə etmək', 'module' => 'quiz'],
            ['name' => 'quiz.update-all', 'display_name' => 'Bütün testləri redaktə etmək', 'module' => 'quiz'],
            ['name' => 'quiz.delete', 'display_name' => 'Test silmək', 'module' => 'quiz'],
            ['name' => 'quiz.delete-all', 'display_name' => 'Bütün testləri silmək', 'module' => 'quiz'],
            ['name' => 'quiz.publish', 'display_name' => 'Test dərc etmək', 'module' => 'quiz'],
            ['name' => 'quiz.publish-all', 'display_name' => 'Bütün testləri dərc etmək', 'module' => 'quiz'],
            ['name' => 'quiz.take', 'display_name' => 'Test vermək', 'module' => 'quiz'],
            ['name' => 'quiz.view-attempts', 'display_name' => 'Test cəhdlərini görmək', 'module' => 'quiz'],

            // Question permissions
            ['name' => 'question.view', 'display_name' => 'Sualları görmək', 'module' => 'question'],
            ['name' => 'question.create', 'display_name' => 'Sual yaratmaq', 'module' => 'question'],
            ['name' => 'question.update', 'display_name' => 'Sual redaktə etmək', 'module' => 'question'],
            ['name' => 'question.delete', 'display_name' => 'Sual silmək', 'module' => 'question'],

            // User permissions
            ['name' => 'user.view', 'display_name' => 'İstifadəçiləri görmək', 'module' => 'user'],
            ['name' => 'user.create', 'display_name' => 'İstifadəçi yaratmaq', 'module' => 'user'],
            ['name' => 'user.update', 'display_name' => 'İstifadəçi redaktə etmək', 'module' => 'user'],
            ['name' => 'user.delete', 'display_name' => 'İstifadəçi silmək', 'module' => 'user'],
            ['name' => 'user.manage', 'display_name' => 'İstifadəçi rollarını idarə etmək', 'module' => 'user'],

            // Role permissions
            ['name' => 'role.view', 'display_name' => 'Rolları görmək', 'module' => 'role'],
            ['name' => 'role.create', 'display_name' => 'Rol yaratmaq', 'module' => 'role'],
            ['name' => 'role.update', 'display_name' => 'Rol redaktə etmək', 'module' => 'role'],
            ['name' => 'role.delete', 'display_name' => 'Rol silmək', 'module' => 'role'],
            ['name' => 'role.manage', 'display_name' => 'Rol və icazələri idarə etmək', 'module' => 'role'],

            // Permission permissions
            ['name' => 'permission.view', 'display_name' => 'İcazələri görmək', 'module' => 'permission'],
            ['name' => 'permission.manage', 'display_name' => 'İcazələri idarə etmək', 'module' => 'permission'],

            // Category permissions
            ['name' => 'category.view', 'display_name' => 'Kateqoriyaları görmək', 'module' => 'category'],
            ['name' => 'category.create', 'display_name' => 'Kateqoriya yaratmaq', 'module' => 'category'],
            ['name' => 'category.update', 'display_name' => 'Kateqoriya redaktə etmək', 'module' => 'category'],
            ['name' => 'category.delete', 'display_name' => 'Kateqoriya silmək', 'module' => 'category'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
