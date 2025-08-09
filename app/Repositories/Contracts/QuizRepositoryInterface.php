<?php

namespace App\Repositories\Contracts;

interface QuizRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveQuizzes();
    public function getDraftQuizzes();
    public function getArchivedQuizzes();
    public function getQuizzesByTeacher(int $teacherId);
    public function getQuizWithQuestions(int $quizId);
    public function getQuizAttempts(int $quizId);
    public function getQuizStatistics(int $quizId);
    public function searchQuizzes(string $search);
    public function getQuizzesByCategory(int $categoryId);
    public function getPopularQuizzes(int $limit = 10);
}
