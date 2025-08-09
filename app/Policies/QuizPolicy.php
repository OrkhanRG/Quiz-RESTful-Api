<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuizPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Quiz $quiz)
    {
        if ($user->hasRole('teacher') || $user->hasPermission('quiz.view-all')) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $quiz->isActive();
        }

        return false;
    }

    public function create(User $user)
    {
        return $user->hasPermission('quiz.create');
    }

    public function update(User $user, Quiz $quiz)
    {
        return $user->hasPermission('quiz.update') &&
            ($user->id === $quiz->teacher_id || $user->hasPermission('quiz.update-all'));
    }

    public function delete(User $user, Quiz $quiz)
    {
        return $user->hasPermission('quiz.delete') &&
            ($user->id === $quiz->teacher_id || $user->hasPermission('quiz.delete-all'));
    }

    public function publish(User $user, Quiz $quiz)
    {
        return $user->hasPermission('quiz.publish') &&
            ($user->id === $quiz->teacher_id || $user->hasPermission('quiz.publish-all'));
    }
}
