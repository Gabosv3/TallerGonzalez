<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    /**
     * El interceptor en AppServiceProvider maneja todo el envío de correos.
     * Esta clase solo se asegura de que el flujo funcione correctamente.
     */
    public function submit(): void
    {
        Log::info('=== RequestPasswordReset::submit() iniciado ===');
        
        try {
            $data = $this->form->getState();
            $email = $data['email'] ?? null;
            
            Log::info("Solicitando reset para: {$email}");
            
            // El Password::sendResetLink() ahora disparará nuestro useResetNotifier
            $status = Password::sendResetLink(['email' => $email]);
            
            Log::info("Estado del reset: {$status}");
            
            if ($status === Password::RESET_LINK_SENT) {
                Log::info("✓ Enlace enviado correctamente");
                Notification::make()
                    ->title(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.success.title'))
                    ->body('Se ha enviado un enlace de restablecimiento a tu correo electrónico.')
                    ->success()
                    ->send();
            } else {
                Log::warning("Fallo al enviar enlace: {$status}");
                Notification::make()
                    ->title(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.title'))
                    ->body(__('filament-panels::pages/auth/password-reset/request-password-reset.notifications.throttled.body'))
                    ->warning()
                    ->send();
            }
            
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
