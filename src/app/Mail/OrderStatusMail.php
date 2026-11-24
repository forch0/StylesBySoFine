<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('items', 'deliveryZone');
    }

    public function envelope(): Envelope
    {
        $status = $this->order->fulfilment_status;
        $subject = match ($status) {
            'shipped' => 'Your order is on its way — '.$this->order->order_number,
            'delivered' => 'Delivered — '.$this->order->order_number,
            'processing' => 'We\'re preparing your order — '.$this->order->order_number,
            'cancelled' => 'Order cancelled — '.$this->order->order_number,
            default => 'Order update — '.$this->order->order_number,
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status',
            with: ['isShipped' => $this->order->fulfilment_status === 'shipped'],
        );
    }
}
