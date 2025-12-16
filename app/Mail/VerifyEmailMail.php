<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\URL;

class VerifyEmailMail extends Mailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $email,
        public int $userId
    )
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->email,
            subject: 'Verifica tu correo electronico - Taller Gonzalez',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Generar URL firmada y temporal (válida por 24 horas)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $this->userId]
        );

        return new Content(
            view: 'auth.verify-email',
            with: [
                'email' => $this->email,
                'userId' => $this->userId,
                'verificationUrl' => $verificationUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
