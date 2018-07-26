<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticateOnceWithBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        try {
            Auth::onceBasic('username');
        } catch (UnauthorizedHttpException $e) {
            return response()->json([
                'success' => false,
                'status' => 401,
                'error' => [
                    'title' => 'Invalid Credentials',
                    'message' => "The provided credentials were not valid.",
                ],
            ], 401);
        }

        return $next($request);
    }
}
