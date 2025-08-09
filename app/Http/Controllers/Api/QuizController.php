<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Http\Requests\SubmitAnswerRequest;
use App\Http\Resources\QuizResource;
use App\Http\Resources\QuizAttemptResource;
use App\Services\QuizService;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(
        QuizService $quizService,
    )
    {
        $this->quizService = $quizService;
    }

    public function index(Request $request)
    {
        try {
            if (auth()->user()->hasRole('teacher')) {
                $quizzes = $this->quizService->getQuizzesByTeacher(auth()->id());
            } else {
                $quizzes = $this->quizService->getActiveQuizzes();
            }

            return QuizResource::collection($quizzes);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Testlər əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CreateQuizRequest $request)
    {
        try {
            $quiz = $this->quizService->createQuiz(
                $request->validated(),
                auth()->id()
            );

            return response()->json([
                'message' => 'Test uğurla yaradıldı',
                'quiz' => new QuizResource($quiz)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Test yaradılarkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $quiz = $this->quizService->getQuizWithQuestions($id);
            return new QuizResource($quiz);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Test tapılmadı',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(UpdateQuizRequest $request, $id)
    {
        try {
            $quiz = $this->quizService->updateQuiz($id, $request->validated());

            return response()->json([
                'message' => 'Test uğurla yeniləndi',
                'quiz' => new QuizResource($quiz)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Test yenilənərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->quizService->deleteQuiz($id);

            return response()->json([
                'message' => 'Test uğurla silindi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Test silinərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function publish($id)
    {
        try {
            $quiz = $this->quizService->publishQuiz($id);

            return response()->json([
                'message' => 'Test uğurla dərc edildi',
                'quiz' => new QuizResource($quiz)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Test dərc edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function archive($id)
    {
        try {
            $quiz = $this->quizService->archiveQuiz($id);

            return response()->json([
                'message' => 'Test uğurla arxivləşdirildi',
                'quiz' => new QuizResource($quiz)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Test arxivləşdirilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function startAttempt($id)
    {
        try {
            $attempt = $this->quizService->startQuizAttempt($id, auth()->id());

            return response()->json([
                'message' => 'Test cəhdi uğurla başladı',
                'attempt' => new QuizAttemptResource($attempt),
                'quiz' => new QuizResource($attempt->quiz->load('questions.options'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Test başladılarkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function submitAnswer(SubmitAnswerRequest $request, $attemptId)
    {
        try {
            $answer = $this->quizService->submitQuizAnswer(
                $attemptId,
                $request->question_id,
                $request->validated()
            );

            return response()->json([
                'message' => 'Cavab uğurla göndərildi',
                'is_correct' => $answer->isCorrect(),
                'points_earned' => $answer->points_earned
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Cavab göndərilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function completeAttempt($attemptId)
    {
        try {
            $attempt = $this->quizService->completeQuizAttempt($attemptId);

            return response()->json([
                'message' => 'Test uğurla tamamlandı',
                'attempt' => new QuizAttemptResource($attempt),
                'results' => [
                    'score' => $attempt->score,
                    'correct_answers' => $attempt->correct_answers,
                    'total_questions' => $attempt->total_questions,
                    'percentage' => $attempt->percentage
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Test tamamlanarkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function abandonAttempt($attemptId)
    {
        try {
            $attempt = $this->quizService->abandonQuizAttempt($attemptId);

            return response()->json([
                'message' => 'Test cəhdi tərk edildi',
                'attempt' => new QuizAttemptResource($attempt)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Test tərk edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function getAttemptResults($attemptId)
    {
        try {
            $attempt = $this->quizService->getAttemptWithResults($attemptId);
            $statistics = $this->quizService->getAttemptStatistics($attemptId);

            return response()->json([
                'attempt' => new QuizAttemptResource($attempt),
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Nəticələr əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function getMyAttempts()
    {
        try {
            $attempts = QuizAttempt::where('student_id', auth()->id())
                ->with(['quiz.category'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return !!$attempts ? QuizAttemptResource::collection($attempts) : [];

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Cəhdlər əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAttempts($id)
    {
        try {
            $attempts = $this->quizService->getQuizAttempts($id);
            return !!$attempts ? QuizAttemptResource::collection($attempts) : [];

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Test cəhdləri əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getQuizStatistics($id)
    {
        try {
            $statistics = $this->quizService->getQuizStatistics($id);
            return response()->json($statistics);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Statistika əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search($search)
    {
        try {
            $quizzes = $this->quizService->searchQuizzes($search);
            return QuizResource::collection($quizzes);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Axtarış zamanı səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByCategory($categoryId)
    {
        try {
            $quizzes = $this->quizService->getQuizzesByCategory($categoryId);
            return QuizResource::collection($quizzes);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Kateqoriya testləri əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPopular()
    {
        try {
            $quizzes = $this->quizService->getPopularQuizzes();

            return !!$quizzes ? QuizResource::collection($quizzes) : [];

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Populyar testlər əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDraft()
    {
        try {
            $quizzes = $this->quizService->getDraftQuizzes();
            return !!$quizzes ? QuizResource::collection($quizzes) : [];

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Qaralama testlər əldə edilərkən səhv baş verdi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
