<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Projede standart success cevabı için kısayol.
     * Controller’larda $this->ok(...) çağırabilirsin.
     */
    protected function ok(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $status);
    }

    /**
     * Projede standart error cevabı için kısayol.
     * Controller’larda $this->fail(...) çağırabilirsin.
     */
    protected function fail(string $message, array $errors = [], int $status = 400): JsonResponse
    {
        return ApiResponse::error($message, $errors, $status);
    }
}
