<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        if (!auth()->user()->hasRole($role)) {
            return response()->json([
                'message' => 'Bu səhifəyə daxil olmaq üçün lazımi səlahiyyətiniz yoxdur.',
                'required_role' => $role
            ], 403);
        }

        return $next($request);
    }
}
