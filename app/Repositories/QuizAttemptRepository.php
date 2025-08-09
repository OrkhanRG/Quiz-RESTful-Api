<?php

namespace App\Repositories;

use App\Repositories\Contracts\QuizAttemptRepositoryInterface;
use App\Models\QuizAttempt;
use App\Enums\QuizAttemptStatus;

class QuizAttemptRepository extends BaseRepository implements QuizAttemptRepositoryInterface
{
    public function __construct(QuizAttempt $quizAttempt)
    {
        parent::__construct($quizAttempt);
    }

    public function getAttemptsByStudent(int $studentId)
    {
        return $this->model->where('student_id', $studentId)
            ->with(['quiz.category'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAttemptsByQuiz(int $quizId)
    {
        return $this->model->where('quiz_id', $quizId)
            ->with(['student'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getCompletedAttempts(int $studentId)
    {
        return $this->model->where('student_id', $studentId)
            ->where('status', QuizAttemptStatus::COMPLETED)
            ->with(['quiz.category'])
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    public function getInProgressAttempts(int $studentId)
    {
        return $this->model->where('student_id', $studentId)
            ->where('status', QuizAttemptStatus::IN_PROGRESS)
            ->with(['quiz.category'])
            ->orderBy('started_at', 'desc')
            ->get();
    }

    public function getAttemptWithAnswers(int $attemptId)
    {
        return $this->model->with(['attemptAnswers.question.options', 'quiz.questions'])
            ->findOrFail($attemptId);
    }

    public function getAttemptStatistics(int $attemptId)
    {
        $attempt = $this->model->with(['attemptAnswers'])->findOrFail($attemptId);

        return [
            'total_questions' => $attempt->total_questions,
            'answered_questions' => $attempt->attemptAnswers()->count(),
            'correct_answers' => $attempt->correct_answers,
            'score' => $attempt->score,
            'percentage' => $attempt->percentage,
            'time_taken' => $attempt->completed_at ? $attempt->started_at->diffInMinutes($attempt->completed_at) : null,
        ];
    }
}
