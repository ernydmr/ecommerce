<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminOnly::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 422 - Validasyon
        $exceptions->render(function (ValidationException $e, $request) {
            return ApiResponse::error('Validasyon hatası', $e->errors(), 422);
        });

        // 401 - Yetkisiz
        $exceptions->render(function (AuthenticationException $e, $request) {
            return ApiResponse::error('Yetkisiz erişim', [], 401);
        });

        // 404 - Bulunamadı (route/model)
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            return ApiResponse::error('Bulunamadı', [], 404);
        });

        // 500 - Yakalanmamış diğer hatalar
        $exceptions->render(function (Throwable $e, $request) {
            \Log::error('Unhandled Exception', [
                'error' => $e->getMessage(),
                'trace' => collect($e->getTrace())->take(5),
            ]);
            return ApiResponse::error('Sunucu hatası', [], 500);
        });
    })->create();