<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccess extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $product;
    public $payment;
    public $orderId;

    public function __construct($user, $product, $payment, $orderId)
    {
        $this->user = $user;
        $this->product = $product;
        $this->payment = $payment;
        $this->orderId = $orderId;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Success - '. $this->product->name.' Purchase Confirmation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_success', 
            with: [
                'user' => $this->user,
                'product' => $this->product,
                'payment' => $this->payment,
                'orderId' => $this->orderId,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}