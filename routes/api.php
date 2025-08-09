<?php

use App\Enums\QuizAttemptStatus;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Services\{
    ImageUploadService,
    StatisticsService
};

use App\Http\Controllers\Api\{
    AuthController,
    QuizController,
    QuestionController,
    CategoryController,
    RoleController,
    PermissionController,
    UserController
};

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::put('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/deactivate-account', [AuthController::class, 'deactivateAccount']);
    Route::post('/send-email-verification', [AuthController::class, 'sendEmailVerification']);
    Route::post('/refresh-token', [AuthController::class, 'refresh']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::get('/categories/search/{search}', [CategoryController::class, 'search']);
    Route::middleware('permission:category.create')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
    });
    Route::middleware('permission:category.update')->group(function () {
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
    });
    Route::middleware('permission:category.delete')->group(function () {
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });
    Route::get('/categories/{category}/statistics', [CategoryController::class, 'getStatistics']);

    // Questions
    Route::middleware('permission:question.view')->group(function () {
        Route::get('/questions', [QuestionController::class, 'index']);
        Route::get('/questions/{question}', [QuestionController::class, 'show']);
        Route::get('/questions/search/{search}', [QuestionController::class, 'search']);
        Route::get('/questions/type/{type}', [QuestionController::class, 'getByType']);
        Route::get('/questions/difficulty/{difficulty}', [QuestionController::class, 'getByDifficulty']);
        Route::get('/questions/random', [QuestionController::class, 'getRandom']);
        Route::get('/my-questions', [QuestionController::class, 'getMyQuestions']);
    });
    Route::middleware('permission:question.create')->group(function () {
        Route::post('/questions', [QuestionController::class, 'store']);
    });
    Route::middleware('permission:question.update')->group(function () {
        Route::put('/questions/{question}', [QuestionController::class, 'update']);
    });
    Route::middleware('permission:question.delete')->group(function () {
        Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);
    });

    // Quizzes
    Route::get('/quizzes', [QuizController::class, 'index']);
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show']);
    Route::get('/quizzes/search/{search}', [QuizController::class, 'search']);
    Route::get('/quizzes/category/{category}', [QuizController::class, 'getByCategory']);
    Route::get('/popular-quizzes', [QuizController::class, 'getPopular']);
    Route::get('/draft-quizzes', [QuizController::class, 'getDraft']);

    Route::middleware('permission:quiz.create')->group(function () {
        Route::post('/quizzes', [QuizController::class, 'store']);
    });
    Route::middleware('permission:quiz.update')->group(function () {
        Route::put('/quizzes/{quiz}', [QuizController::class, 'update']);
    });
    Route::middleware('permission:quiz.delete')->group(function () {
        Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy']);
    });
    Route::middleware('permission:quiz.publish')->group(function () {
        Route::post('/quizzes/{quiz}/publish', [QuizController::class, 'publish']);
        Route::post('/quizzes/{quiz}/archive', [QuizController::class, 'archive']);
    });

    // Quiz taking
    Route::middleware('permission:quiz.take')->group(function () {
        Route::post('/quizzes/{quiz}/start', [QuizController::class, 'startAttempt']);
        Route::post('/attempts/{attempt}/answer', [QuizController::class, 'submitAnswer']);
        Route::post('/attempts/{attempt}/complete', [QuizController::class, 'completeAttempt']);
        Route::post('/attempts/{attempt}/abandon', [QuizController::class, 'abandonAttempt']);
        Route::get('/attempts/{attempt}/results', [QuizController::class, 'getAttemptResults']);
        Route::get('/my-attempts', [QuizController::class, 'getMyAttempts']);
    });

    // Quiz attempts management
    Route::middleware('permission:quiz.view-attempts')->group(function () {
        Route::get('/quizzes/{quiz}/attempts', [QuizController::class, 'getAttempts']);
        Route::get('/quizzes/{quiz}/statistics', [QuizController::class, 'getQuizStatistics']);
    });

    // User Management
    Route::middleware('permission:user.view')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::get('/users/{user}/permissions', [UserController::class, 'getUserPermissions']);
        Route::get('/users/{user}/statistics', [UserController::class, 'getUserStatistics']);
        Route::get('/users/search/{search}', [UserController::class, 'search']);
        Route::get('/teachers', [UserController::class, 'getTeachers']);
        Route::get('/students', [UserController::class, 'getStudents']);
    });
    Route::middleware('permission:user.manage')->group(function () {
        Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole']);
        Route::post('/users/{user}/remove-role', [UserController::class, 'removeRole']);
        Route::put('/users/{user}/status', [UserController::class, 'updateStatus']);
        Route::get('/active-users', [UserController::class, 'getActiveUsers']);
    });

    // Role Management
    Route::middleware('permission:role.view')->group(function () {
        Route::get('/roles', [RoleController::class, 'index']);
        Route::get('/roles/{role}', [RoleController::class, 'show']);
        Route::get('/roles/{role}/users', [RoleController::class, 'getRoleUsers']);
    });
    Route::middleware('permission:role.manage')->group(function () {
        Route::post('/roles', [RoleController::class, 'store']);
        Route::put('/roles/{role}', [RoleController::class, 'update']);
        Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
        Route::post('/roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions']);
        Route::post('/roles/{role}/remove-permissions', [RoleController::class, 'removePermissions']);
        Route::post('/roles/{role}/assign-user', [RoleController::class, 'assignToUser']);
    });

    // Permission Management
    Route::middleware('permission:permission.view')->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::get('/permissions/module/{module}', [PermissionController::class, 'getByModule']);
    });
    Route::middleware('permission:permission.manage')->group(function () {
        Route::post('/permissions', [PermissionController::class, 'store']);
        Route::put('/permissions/{permission}', [PermissionController::class, 'update']);
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy']);
    });

    //-- Additional routes

    // Statistics
    Route::get('/dashboard/statistics', function() {
        $statisticsService = app(StatisticsService::class);
        return response()->json($statisticsService->getDashboardStats(auth()->id()));
    });

    // Image upload
    Route::post('/upload/question-image', function(Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imageService = app(ImageUploadService::class);
        $path = $imageService->uploadQuestionImage($request->file('image'));

        return response()->json([
            'message' => 'Şəkil uğurla yükləndi',
            'path' => $path,
            'url' => $imageService->getImageUrl($path)
        ]);
    })->middleware('permission:question.create');

    // Report routes
    Route::get('/reports/quiz/{quiz}', function($quizId) {
        $quiz = Quiz::with(['attempts.student', 'questions'])->findOrFail($quizId);

        return response()->json([
            'quiz' => new QuizResource($quiz),
            'summary' => [
                'total_attempts' => $quiz->attempts->count(),
                'completed_attempts' => $quiz->attempts->where('status', QuizAttemptStatus::COMPLETED)->count(),
                'average_score' => $quiz->attempts->where('status', QuizAttemptStatus::COMPLETED)->avg('score'),
                'pass_rate' => $quiz->attempts->where('percentage', '>=', 60)->count() / max($quiz->attempts->count(), 1) * 100
            ]
        ]);
    })->middleware('permission:quiz.view-attempts');
});
