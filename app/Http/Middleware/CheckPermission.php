<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        if (!auth()->user()->hasPermission($permission)) {
            return response()->json([
                'message' => 'Bu əməliyyat üçün icazəniz yoxdur.',
                'required_permission' => $permission
            ], 403);
        }

        return $next($request);
    }
}
