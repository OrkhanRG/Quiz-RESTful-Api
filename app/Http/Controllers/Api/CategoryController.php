<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getActiveCategories();
        return CategoryResource::collection($categories);
    }

    public function store(CreateCategoryRequest $request)
    {
        try {
            $data = $request->only("name", "description", "slug", "status");
            if (@$data["slug"]) {
                $data["slug"] = Str::slug($data["slug"]);
            }

            $category = $this->categoryService->createCategory($data);

            return response()->json([
                'message' => 'Kateqoriya uğurla yaradıldı',
                'category' => new CategoryResource($category)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kateqoriya yaradılarkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = $this->categoryService->getCategoryWithQuizzes($id);
            return new CategoryResource($category);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kateqoriya tapılmadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $data = $request->only("name", "description", "slug", "status");
            if (@$data["slug"]) {
                $data["slug"] = Str::slug($data["slug"]);
            }

            $category = $this->categoryService->updateCategory($id, $data);

            return response()->json([
                'message' => 'Kateqoriya uğurla yeniləndi',
                'category' => new CategoryResource($category)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kateqoriya yenilənərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->categoryService->deleteCategory($id);

            return response()->json([
                'message' => 'Kateqoriya uğurla silindi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kateqoriya silinərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search($search)
    {
        $categories = $this->categoryService->searchCategories($search);
        return CategoryResource::collection($categories);
    }

    public function getStatistics($id)
    {
        try {
            $statistics = $this->categoryService->getCategoryStatistics($id);
            return response()->json($statistics);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Statistika əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
