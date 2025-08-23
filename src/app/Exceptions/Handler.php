<?php

namespace App\Exceptions;

use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Which types of exceptions are not reported.
     */
    protected $dontReport = [
        //
    ];

    /**
     * Inputs that are never flashed to session on validation errors.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        // buraya reportable/Renderable closure'lar ekleyebilirsin
    }

    public function render($request, Throwable $e)
    {


        if ($e instanceof AuthenticationException) {
            return ApiResponse::error('Unauthenticated', [], 401);
        }

        if ($e instanceof AuthorizationException) {
            return ApiResponse::error('Forbidden', [], 403);
        }

        if ($e instanceof ModelNotFoundException) {
            return ApiResponse::error('Not Found', [], 404);
        }

        // Geliştirme modunda (APP_DEBUG=true) stack trace görmek istersen parent'a bırak:
        if (config('app.debug')) {
            return parent::render($request, $e);
        }

        return ApiResponse::error('Server Error', [], 500);
    }
}
