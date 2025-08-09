<?php

namespace App\Repositories;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use App\Enums\UserStatus;

use App\Enums\{
    QuizAttemptStatus,
    QuizStatus
};

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getActiveUsers()
    {
        return $this->model->where('status', UserStatus::ACTIVE)
            ->with(['roles'])
            ->get();
    }

    public function getUsersByRole(string $roleName)
    {
        return $this->model->whereHas('roles', function($query) use ($roleName) {
            $query->where('name', $roleName);
        })
            ->where('status', UserStatus::ACTIVE)
            ->get();
    }

    public function getUserWithRoles(int $userId)
    {
        return $this->model->with(['roles.permissions'])
            ->findOrFail($userId);
    }

    public function getUserStatistics(int $userId)
    {
        $user = $this->findOrFail($userId);

        if ($user->hasRole('teacher')) {
            return [
                'total_quizzes' => $user->createdQuizzes()->count(),
                'active_quizzes' => $user->createdQuizzes()->where('status', QuizStatus::ACTIVE)->count(),
                'total_questions' => $user->createdQuestions()->count(),
                'total_attempts_on_quizzes' => $user->createdQuizzes()->withCount('attempts')->get()->sum('attempts_count'),
            ];
        }

        if ($user->hasRole('student')) {
            return [
                'total_attempts' => $user->quizAttempts()->count(),
                'completed_attempts' => $user->quizAttempts()->where('status', QuizAttemptStatus::COMPLETED)->count(),
                'average_score' => $user->quizAttempts()->where('status', QuizAttemptStatus::COMPLETED)->avg('score'),
                'highest_score' => $user->quizAttempts()->where('status', QuizAttemptStatus::COMPLETED)->max('score'),
            ];
        }

        return [];
    }

    public function searchUsers(string $search)
    {
        return $this->model->where('name', 'LIKE', "%{$search}%")
            ->orWhere('email', 'LIKE', "%{$search}%")
            ->where('status', '!=', UserStatus::INACTIVE)
            ->with(['roles'])
            ->get();
    }

    public function getTeachers()
    {
        return $this->getUsersByRole('teacher');
    }

    public function getStudents()
    {
        return $this->getUsersByRole('student');
    }
}
