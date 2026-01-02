<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    /**
     * Create a new message instance.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
        Log::info('ResetPasswordMail construido con URL: ' . $url);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('ResetPasswordMail::build() ejecutándose');
        
        $message = $this
            ->subject('Restablecer Contraseña - ' . config('app.name'))
            ->markdown('emails.auth.reset-password');
        
        Log::info('ResetPasswordMail construido exitosamente');
        
        return $message;
    }
}
