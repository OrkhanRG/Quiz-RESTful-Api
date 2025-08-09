<?php

namespace App\Repositories;

use App\Repositories\Contracts\QuestionRepositoryInterface;
use App\Models\Question;
use App\Enums\QuestionType;

class QuestionRepository extends BaseRepository implements QuestionRepositoryInterface
{
    public function __construct(Question $question)
    {
        parent::__construct($question);
    }

    public function getQuestionsByCreator(int $userId)
    {
        return $this->model->where('created_by', $userId)
            ->with(['options'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getQuestionWithOptions(int $questionId)
    {
        return $this->model->with(['options'])->findOrFail($questionId);
    }

    public function getActiveQuestions()
    {
        return $this->model->where('status', '1')
            ->with(['options'])
            ->get();
    }

    public function getQuestionsByType(string $type)
    {
        return $this->model->where('type', $type)
            ->where('status', '1')
            ->with(['options'])
            ->get();
    }

    public function getQuestionsByDifficulty(string $difficulty)
    {
        return $this->model->where('difficulty', $difficulty)
            ->where('status', '1')
            ->with(['options'])
            ->get();
    }

    public function searchQuestions(string $search)
    {
        return $this->model->where('question_text', 'LIKE', "%{$search}%")
            ->where('status', '1')
            ->with(['options'])
            ->get();
    }

    public function getRandomQuestions(int $count)
    {
        return $this->model->where('status', '1')
            ->inRandomOrder()
            ->limit($count)
            ->with(['options'])
            ->get();
    }

    public function getQuestionsByCategory(int $categoryId)
    {
        return $this->model->whereHas('quizzes', function($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })
            ->where('status', '1')
            ->with(['options'])
            ->get();
    }
}
