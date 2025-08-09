<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\CreateQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Services\QuestionService;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    protected $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    public function index(Request $request)
    {
        $questions = $this->questionService->getAllQuestions();
        return QuestionResource::collection($questions);
    }

    public function store(CreateQuestionRequest $request)
    {
        try {
            $question = $this->questionService->createQuestion(
                $request->validated(),
                auth()->id()
            );

            return response()->json([
                'message' => 'Sual uğurla yaradıldı',
                'question' => new QuestionResource($question)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sual yaradılarkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $question = $this->questionService->getQuestionWithOptions((int)$id);
            return new QuestionResource($question);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sual tapılmadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(UpdateQuestionRequest $request, $id)
    {
        try {
            $question = $this->questionService->updateQuestion($id, $request->validated());

            return response()->json([
                'message' => 'Sual uğurla yeniləndi',
                'question' => new QuestionResource($question)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sual yenilənərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->questionService->deleteQuestion($id);

            return response()->json([
                'message' => 'Sual uğurla silindi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sual silinərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search($search)
    {
        $questions = $this->questionService->searchQuestions($search);
        return QuestionResource::collection($questions);
    }

    public function getByType($type)
    {
        $questions = $this->questionService->getQuestionsByType($type);
        return QuestionResource::collection($questions);
    }

    public function getByDifficulty($difficulty)
    {
        $questions = $this->questionService->getQuestionsByDifficulty($difficulty);
        return QuestionResource::collection($questions);
    }

    public function getRandom(Request $request)
    {
        $count = $request->get('count', 10);
        $questions = $this->questionService->getRandomQuestions($count);
        return QuestionResource::collection($questions);
    }

    public function getMyQuestions()
    {
        $questions = $this->questionService->getQuestionsByCreator(auth()->id());
        return QuestionResource::collection($questions);
    }
}
