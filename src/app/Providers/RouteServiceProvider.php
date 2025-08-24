<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /**
         * Genel API rate limit (ör. /products, /categories)
         * routes/api.php içinde 'throttle:api' olarak kullanılabilir.
         */
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(100)->by(optional($request->user())->id ?: $request->ip());
        });

        /**
         * Sipariş oluşturma için daha sıkı limit.
         * routes/api.php içinde 'throttle:orders' kullanıyoruz.
         */
        RateLimiter::for('orders', function (Request $request) {
            return Limit::perMinute(10)->by(optional($request->user())->id ?: $request->ip());
        });

    }
}
