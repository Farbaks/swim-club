<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {   

        if(!in_array($request->userRole, $roles)) {
            return response()->json([
                'status' => 403,
                'message' => 'You\'re not authorized to perform this action.',
                'data' => []
            ], 403);
        }
        return $next($request);
    }
}
