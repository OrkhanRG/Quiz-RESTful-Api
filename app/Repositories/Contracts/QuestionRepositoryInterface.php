<?php

namespace App\Repositories\Contracts;

interface QuestionRepositoryInterface extends BaseRepositoryInterface
{
    public function getQuestionsByCreator(int $userId);
    public function getQuestionWithOptions(int $questionId);
    public function getActiveQuestions();
    public function getQuestionsByType(string $type);
    public function getQuestionsByDifficulty(string $difficulty);
    public function searchQuestions(string $search);
    public function getRandomQuestions(int $count);
    public function getQuestionsByCategory(int $categoryId);
}
