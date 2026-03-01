<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Order extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $body;

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order',
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'emails.order',
            with: [
                'body' => $this->body,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
