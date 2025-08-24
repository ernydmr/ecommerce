<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Hizmet bağları
        $this->app->bind(
            \App\Services\Contracts\OrderServiceInterface::class,
            \App\Services\OrderService::class
        );

        // Eğer Cart için interface kullandıysan:
        if (interface_exists(\App\Services\Contracts\CartServiceInterface::class)) {
            $this->app->bind(
                \App\Services\Contracts\CartServiceInterface::class,
                \App\Services\CartService::class
            );
        }
    }

    public function boot(): void {}
}
