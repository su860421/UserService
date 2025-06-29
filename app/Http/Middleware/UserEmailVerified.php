<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserEmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => '未授權的請求',
                'error' => 'UNAUTHORIZED'
            ], 401);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'message' => '請先驗證您的電子郵件地址',
                'error' => 'EMAIL_NOT_VERIFIED'
            ], 403);
        }

        return $next($request);
    }
}
