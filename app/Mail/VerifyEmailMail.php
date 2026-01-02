<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
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
            from: config('mail.from.address'),
            replyTo: [['address' => config('mail.from.address'), 'name' => config('mail.from.name')]],
            to: $this->email,
            subject: 'Verifica tu correo electronico - Taller Gonzalez',
        );
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Mailer' => 'Taller Gonzalez',
                'X-Priority' => '3',
                'X-MSMail-Priority' => 'Normal',
                'Importance' => 'Normal',
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Generar URL firmada y temporal (válida por 24 horas)
        // Usar el dominio real en lugar de localhost
        $appUrl = config('app.url');
        if (config('app.env') === 'local' || strpos($appUrl, 'localhost') !== false) {
            // En desarrollo, usar la URL configurada
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addHours(24),
                ['id' => $this->userId]
            );
        } else {
            // En producción, usar la URL real
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addHours(24),
                ['id' => $this->userId]
            );
        }

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
