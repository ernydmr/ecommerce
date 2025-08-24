<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // auth:api kullanılmadıysa bile hoş dursun
        if (!$user) {
            return ApiResponse::error('Kimlik doğrulama gerekli', [], 401);
        }

        if ($user->role !== 'admin') {
            return ApiResponse::error('Admin gerekli', [], 403);
        }

        return $next($request);
    }
}
