<?php

namespace App\Http\Middleware;

use App\Models\Quiz;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckQuizOwnership
{
    public function handle(Request $request, Closure $next)
    {
        $quizId = $request->route('quiz');
        $quiz = Quiz::find($quizId);

        if (!$quiz) {
            return response()->json(['message' => 'Test tapılmadı'], Response::HTTP_NOT_FOUND);
        }

        if ($quiz->teacher_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Bu test sizə aid deyil'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
