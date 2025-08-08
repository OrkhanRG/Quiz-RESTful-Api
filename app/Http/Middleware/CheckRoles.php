<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        if (!auth()->user()->hasAnyRole($roles)) {
            return response()->json([
                'message' => 'Bu səhifəyə daxil olmaq üçün lazımi səlahiyyətiniz yoxdur.',
                'required_roles' => $roles
            ], 403);
        }

        return $next($request);
    }
}
