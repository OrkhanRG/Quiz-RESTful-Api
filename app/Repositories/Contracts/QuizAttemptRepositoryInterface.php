<?php

namespace App\Repositories\Contracts;

interface QuizAttemptRepositoryInterface extends BaseRepositoryInterface
{
    public function getAttemptsByStudent(int $studentId);
    public function getAttemptsByQuiz(int $quizId);
    public function getCompletedAttempts(int $studentId);
    public function getInProgressAttempts(int $studentId);
    public function getAttemptWithAnswers(int $attemptId);
    public function getAttemptStatistics(int $attemptId);
}
