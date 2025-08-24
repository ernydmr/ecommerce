<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmation
{



    use InteractsWithQueue;
    
    public bool $afterCommit = true; // ← commit’ten sonra çalış


    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(\App\Events\OrderCreated $event): void
    {
        $order = $event->order->loadMissing('user');

        Log::info('OrderCreated', ['order_id' => $order->id, 'user' => $order->user->email]);

        Mail::to($order->user->email)
            ->send(new \App\Mail\OrderConfirmationMail($order));
    }
}
