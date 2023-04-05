<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use STS\JWT\Facades\JWT;
use Symfony\Component\HttpFoundation\Response;

class AuthGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $encrypted = $request->bearerToken();

        if(!$encrypted){
            return response()->json([
                'status' => 401,
                'message' => 'No authorization sent in header.',
                'data' => []
            ], 401);
        }

        $token = JWT::parse($encrypted);

        if(!$token->isValid(env('APP_KEY'))) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid token sent in header.',
                'data' => []
            ], 401);
        }
        $checkUser = User::find($token->get('userId'));

        if(!$checkUser) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized access.',
                'data' => []
            ], 401);
        }

        $request->merge(['userId' => $token->get('userId'), 'userRole' => $token->get('userRole')]);

        return $next($request);
    }
}
