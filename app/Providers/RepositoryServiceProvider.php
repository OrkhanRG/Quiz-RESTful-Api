<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\{
    QuizRepositoryInterface,
    QuestionRepositoryInterface,
    CategoryRepositoryInterface,
    UserRepositoryInterface,
    RoleRepositoryInterface,
    PermissionRepositoryInterface,
    QuizAttemptRepositoryInterface
};

use App\Repositories\{
    QuizRepository,
    QuestionRepository,
    CategoryRepository,
    UserRepository,
    RoleRepository,
    PermissionRepository,
    QuizAttemptRepository
};

use App\Models\{
    Quiz,
    Question,
    Category,
    User,
    Role,
    Permission,
    QuizAttempt
};

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(QuizRepositoryInterface::class, function() {
            return new QuizRepository(new Quiz());
        });

        $this->app->bind(QuestionRepositoryInterface::class, function() {
            return new QuestionRepository(new Question());
        });

        $this->app->bind(CategoryRepositoryInterface::class, function() {
            return new CategoryRepository(new Category());
        });

        $this->app->bind(UserRepositoryInterface::class, function() {
            return new UserRepository(new User());
        });

        $this->app->bind(RoleRepositoryInterface::class, function() {
            return new RoleRepository(new Role());
        });

        $this->app->bind(PermissionRepositoryInterface::class, function() {
            return new PermissionRepository(new Permission());
        });

        $this->app->bind(QuizAttemptRepositoryInterface::class, function() {
            return new QuizAttemptRepository(new QuizAttempt());
        });
    }

    public function boot()
    {
        //
    }
}
