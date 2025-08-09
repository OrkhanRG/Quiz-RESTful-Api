<?php

namespace App\Repositories;

use App\Repositories\Contracts\QuizRepositoryInterface;
use App\Models\Quiz;
use App\Enums\{
    QuizStatus,
    QuizAttemptStatus
};

class QuizRepository extends BaseRepository implements QuizRepositoryInterface
{
    public function __construct(Quiz $quiz)
    {
        parent::__construct($quiz);
    }

    public function getActiveQuizzes()
    {
        return $this->model->where('status', QuizStatus::ACTIVE)
            ->where(function($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->with(['category', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getDraftQuizzes()
    {
        return $this->model->where('status', QuizStatus::DRAFT)
            ->with(['category', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getArchivedQuizzes()
    {
        return $this->model->where('status', QuizStatus::ARCHIVED)
            ->with(['category', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getQuizzesByTeacher(int $teacherId)
    {
        return $this->model->where('teacher_id', $teacherId)
            ->with(['category', 'questions'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getQuizWithQuestions(int $quizId)
    {
        return $this->model->with(['questions.options', 'category', 'teacher'])
            ->findOrFail($quizId);
    }

    public function getQuizAttempts(int $quizId)
    {
        return $this->model->findOrFail($quizId)
            ->attempts()
            ->with(['student'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getQuizStatistics(int $quizId)
    {
        $quiz = $this->findOrFail($quizId);
        $attempts = $quiz->attempts()->where('status', QuizAttemptStatus::COMPLETED)->get();

        return [
            'total_attempts' => $attempts->count(),
            'average_score' => $attempts->avg('score'),
            'highest_score' => $attempts->max('score'),
            'lowest_score' => $attempts->min('score'),
            'completion_rate' => $quiz->attempts()->where('status', QuizAttemptStatus::COMPLETED)->count() / max($quiz->attempts()->count(), 1) * 100,
        ];
    }

    public function searchQuizzes(string $search)
    {
        return $this->model->where('title', 'LIKE', "%{$search}%")
            ->orWhere('description', 'LIKE', "%{$search}%")
            ->with(['category', 'teacher'])
            ->get();
    }

    public function getQuizzesByCategory(int $categoryId)
    {
        return $this->model->where('category_id', $categoryId)
            ->where('status', QuizStatus::ACTIVE)
            ->with(['teacher'])
            ->get();
    }

    public function getPopularQuizzes(int $limit = 10)
    {
        return $this->model->withCount('attempts')
            ->where('status', QuizStatus::ACTIVE)
            ->orderBy('attempts_count', 'desc')
            ->limit($limit)
            ->with(['category', 'teacher'])
            ->get();
    }
}
