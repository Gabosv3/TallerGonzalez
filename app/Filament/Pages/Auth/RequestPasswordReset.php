<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    /**
     * Override the default submit method to add custom logging and email handling
     */
    public function submit(): void
    {
        Log::info('=== INICIO: RequestPasswordReset::submit() ===');
        
        try {
            $data = $this->form->getState();
            Log::info('Datos del formulario:', $data);
            
            $email = $data['email'] ?? null;
            
            if (!$email) {
                Log::warning('No se proporcionó email');
                Notification::make()
                    ->title(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.title'))
                    ->body(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.body'))
                    ->danger()
                    ->send();
                return;
            }
            
            Log::info("Intentando resetear contraseña para: {$email}");
            
            // Usar el broker de contraseña de Laravel
            $status = Password::sendResetLink(
                ['email' => $email],
                function ($user, $token) use ($email) {
                    Log::info("Enviando token al usuario: {$email}");
                    
                    // Construir URL de reset
                    $url = route('filament.administrativo.auth.password-reset.reset', [
                        'token' => $token,
                        'email' => $email,
                    ]);
                    
                    Log::info("URL de reset generado: {$url}");
                    
                    try {
                        // Enviar correo manualmente
                        Mail::to($email)->send(new ResetPasswordMail($url));
                        Log::info("Correo enviado exitosamente a: {$email}");
                    } catch (\Exception $e) {
                        Log::error("Error enviando correo: " . $e->getMessage());
                        Log::error("Stack trace: " . $e->getTraceAsString());
                        throw $e;
                    }
                }
            );
            
            Log::info("Status del Password::sendResetLink: " . $status);
            
            if ($status === Password::RESET_LINK_SENT) {
                Log::info("Enlace enviado correctamente");
                Notification::make()
                    ->title(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.success.title'))
                    ->success()
                    ->send();
            } else {
                Log::warning("Error al enviar enlace: {$status}");
                Notification::make()
                    ->title(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.title'))
                    ->danger()
                    ->send();
            }
            
        } catch (\Exception $e) {
            Log::error('Error en RequestPasswordReset::submit()');
            Log::error('Mensaje: ' . $e->getMessage());
            Log::error('Stack: ' . $e->getTraceAsString());
            
            Notification::make()
                ->title('Error')
                ->body('Error al procesar la solicitud: ' . $e->getMessage())
                ->danger()
                ->send();
        }
        
        Log::info('=== FIN: RequestPasswordReset::submit() ===');
    }

    public function register(): void
    {
        Log::info('RequestPasswordReset::register() llamado');
        parent::register();
    }
}
