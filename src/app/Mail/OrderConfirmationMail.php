<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
// use Illuminate\Contracts\Queue\ShouldQueue; // Kuyruk kullanacaksan aç

class OrderConfirmationMail extends Mailable /* implements ShouldQueue */
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        // Siparişi mailable içine al
        $this->order = $order->loadMissing('items.product', 'user');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sipariş Onayı #' . $this->order->id
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.order_confirmation',
            with: [
                'order' => $this->order,
                'user'  => $this->order->user,
                'items' => $this->order->items,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
