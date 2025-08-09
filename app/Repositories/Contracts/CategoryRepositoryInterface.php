<?php

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveCategories();
    public function getCategoryWithQuizzes(int $categoryId);
    public function getCategoryStatistics(int $categoryId);
    public function searchCategories(string $search);
}
