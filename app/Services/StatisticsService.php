<?php

namespace App\Services;

use App\Enums\{
    QuizAttemptStatus,
    QuizStatus,
    UserStatus
};

use App\Models\{
    Category,
    Question,
    Quiz,
    QuizAttempt,
    User
};


class StatisticsService
{
    public function getDashboardStats(int $userId): array
    {
        $user = User::with('roles')->findOrFail($userId);

        if ($user->hasRole('teacher')) {
            return $this->getTeacherStats($userId);
        }

        if ($user->hasRole('student')) {
            return $this->getStudentStats($userId);
        }

        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            return $this->getAdminStats();
        }

        return [];
    }

    protected function getTeacherStats(int $teacherId): array
    {
        return [
            'total_quizzes' => Quiz::where('teacher_id', $teacherId)->count(),
            'active_quizzes' => Quiz::where('teacher_id', $teacherId)
                ->where('status', QuizStatus::ACTIVE)
                ->count(),
            'total_questions' => Question::where('created_by', $teacherId)->count(),
            'total_attempts' => QuizAttempt::whereHas('quiz', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })->count(),
            'completed_attempts' => QuizAttempt::whereHas('quiz', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })->where('status', QuizAttemptStatus::COMPLETED)->count()
        ];
    }

    protected function getStudentStats(int $studentId): array
    {
        return [
            'total_attempts' => QuizAttempt::where('student_id', $studentId)->count(),
            'completed_attempts' => QuizAttempt::where('student_id', $studentId)
                ->where('status', QuizAttemptStatus::COMPLETED)
                ->count(),
            'average_score' => QuizAttempt::where('student_id', $studentId)
                ->where('status', QuizAttemptStatus::COMPLETED)
                ->avg('score'),
            'highest_score' => QuizAttempt::where('student_id', $studentId)
                ->where('status', QuizAttemptStatus::COMPLETED)
                ->max('score'),
            'total_points_earned' => QuizAttempt::where('student_id', $studentId)
                ->where('status', QuizAttemptStatus::COMPLETED)
                ->sum('score')
        ];
    }

    protected function getAdminStats(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('status', UserStatus::ACTIVE)->count(),
            'total_teachers' => User::whereHas('roles', function($q) {
                $q->where('name', 'teacher');
            })->count(),
            'total_students' => User::whereHas('roles', function($q) {
                $q->where('name', 'student');
            })->count(),
            'total_quizzes' => Quiz::count(),
            'active_quizzes' => Quiz::where('status', QuizStatus::ACTIVE)->count(),
            'total_questions' => Question::count(),
            'total_categories' => Category::count(),
            'total_attempts' => QuizAttempt::count(),
            'completed_attempts' => QuizAttempt::where('status', QuizAttemptStatus::COMPLETED)->count()
        ];
    }
}
