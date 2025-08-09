<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->all();
    }

    public function getActiveCategories()
    {
        return $this->categoryRepository->getActiveCategories();
    }

    public function getCategoryWithQuizzes(int $categoryId)
    {
        return $this->categoryRepository->getCategoryWithQuizzes($categoryId);
    }

    public function createCategory(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $categoryId, array $data): Category
    {
        return $this->categoryRepository->update($categoryId, $data);
    }

    public function deleteCategory(int $categoryId): bool
    {
        return $this->categoryRepository->delete($categoryId);
    }

    public function getCategoryStatistics(int $categoryId)
    {
        return $this->categoryRepository->getCategoryStatistics($categoryId);
    }

    public function searchCategories(string $search)
    {
        return $this->categoryRepository->searchCategories($search);
    }
}
