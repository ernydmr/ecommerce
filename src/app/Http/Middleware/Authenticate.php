<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * API isteklerinde yetkisiz durumda redirect yapma;
     * AuthenticationException atılsın ve biz 401 JSON dönelim.
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}
