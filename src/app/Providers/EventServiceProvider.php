<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Listeners\SendOrderConfirmation;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderCreated::class => [
            SendOrderConfirmation::class,
        ],
    ];

    public function boot(): void {}
}
