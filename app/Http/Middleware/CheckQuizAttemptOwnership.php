<?php

namespace App\Http\Middleware;

use App\Models\QuizAttempt;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckQuizAttemptOwnership
{
    public function handle(Request $request, Closure $next)
    {
        $attemptId = $request->route('attempt');
        $attempt = QuizAttempt::find($attemptId);

        if (!$attempt) {
            return response()->json(['message' => 'Test cəhdi tapılmadı'], Response::HTTP_NOT_FOUND);
        }

        if ($attempt->student_id !== auth()->id() && !auth()->user()->hasRole('teacher')) {
            return response()->json(['message' => 'Bu cəhd sizə aid deyil'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}

