<?php

namespace App\Services;

use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class CustomPasswordBroker extends PasswordBroker
{
    /**
     * Send a password reset link to a user.
     *
     * @param  array  $credentials
     * @param  callable|null  $callback
     * @return string
     */
    public function sendResetLink(array $credentials, callable $callback = null)
    {
        Log::info('=== CustomPasswordBroker::sendResetLink() ACTIVADO ===');

        // Find the user
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            Log::warning('Usuario no encontrado para: ' . ($credentials['email'] ?? 'unknown'));
            return static::INVALID_USER;
        }

        Log::info("Usuario encontrado: {$user->email}");

        // Create the password reset token
        $token = $this->tokens->create($user);

        Log::info("Token generado: {$token}");

        // Enviar el correo AQUÍ, DIRECTAMENTE, sin depender de callbacks ni de Filament
        try {
            // Construir la URL de reset usando la ruta de Filament
            $url = route('filament.administrativo.auth.password-reset.reset', [
                'token' => $token,
                'email' => $user->email,
            ]);

            Log::info("URL de reset generada: {$url}");

            // Enviar el correo
            Mail::to($user->email)->send(new ResetPasswordMail($url));

            Log::info("✓ Correo enviado EXITOSAMENTE a: {$user->email}");

        } catch (\Exception $e) {
            Log::error("✗ ERROR enviando correo: " . $e->getMessage());
            Log::error("Línea: " . $e->getLine());
            Log::error("Stack trace: " . $e->getTraceAsString());
            // No relanzar - dejar que continúe el proceso
        }

        Log::info('=== CustomPasswordBroker::sendResetLink() COMPLETADO ===');

        return static::RESET_LINK_SENT;
    }
}

