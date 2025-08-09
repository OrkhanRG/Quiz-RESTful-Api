<?php

namespace App\Repositories;

use App\Enums\QuizStatus;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $category)
    {
        parent::__construct($category);
    }

    public function getActiveCategories()
    {
        return $this->model->where('status', '1')
            ->orderBy('name')
            ->get();
    }

    public function getCategoryWithQuizzes(int $categoryId)
    {
        return $this->model->with(['quizzes' => function($query) {
            $query->where('status', QuizStatus::ACTIVE);
        }])
            ->findOrFail($categoryId);
    }

    public function getCategoryStatistics(int $categoryId)
    {
        $category = $this->findOrFail($categoryId);

        return [
            'total_quizzes' => $category->quizzes()->count(),
            'active_quizzes' => $category->quizzes()->where('status', QuizStatus::ACTIVE)->count(),
            'draft_quizzes' => $category->quizzes()->where('status', QuizStatus::DRAFT)->count(),
            'total_attempts' => $category->quizzes()->withCount('attempts')->get()->sum('attempts_count'),
        ];
    }

    public function searchCategories(string $search)
    {
        return $this->model->where('name', 'LIKE', "%{$search}%")
            ->orWhere('description', 'LIKE', "%{$search}%")
            ->where('status', '1')
            ->get();
    }
}
