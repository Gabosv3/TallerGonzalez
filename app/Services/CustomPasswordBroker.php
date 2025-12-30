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
        Log::info('=== CustomPasswordBroker::sendResetLink() activado ===');
        Log::info('Credenciales recibidas', $credentials);

        // Find the user
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            Log::warning('Usuario no encontrado para: ' . ($credentials['email'] ?? 'unknown'));
            return static::INVALID_USER;
        }

        // Create the password reset token
        $token = $this->tokens->create($user);

        Log::info("Token generado: {$token}");
        Log::info("Usuario: " . $user->email);

        // Send the password reset link to the user
        $callback = $callback ?: function ($user, $token) {
            // Enviar el correo directamente aquí
            try {
                $url = route('filament.administrativo.auth.password-reset.reset', [
                    'token' => $token,
                    'email' => $user->email,
                ]);

                Log::info("URL de reset: {$url}");

                Mail::to($user->email)->send(new ResetPasswordMail($url));

                Log::info("✓ Correo enviado a: " . $user->email);
            } catch (\Exception $e) {
                Log::error("✗ Error enviando correo: " . $e->getMessage());
                Log::error("Stack: " . $e->getTraceAsString());
                throw $e;
            }
        };

        $user->sendPasswordResetNotification($token);

        Log::info('=== CustomPasswordBroker::sendResetLink() completado ===');

        return static::RESET_LINK_SENT;
    }
}
