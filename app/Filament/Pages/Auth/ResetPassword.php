<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\ResetPassword as BaseResetPassword;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Filament\Notifications\Notification;

class ResetPassword extends BaseResetPassword
{
    /**
     * Override the submit method to add custom logging
     */
    public function submit(): void
    {
        Log::info('=== INICIO: ResetPassword::submit() ===');
        
        try {
            $data = $this->form->getState();
            Log::info('Datos del formulario recibidos');
            
            $email = $data['email'] ?? null;
            $token = $data['token'] ?? null;
            
            Log::info("Email: {$email}, Token presente: " . (!empty($token) ? 'Sí' : 'No'));
            
            // Llamar al método padre que hace la lógica
            parent::submit();
            
            Log::info('ResetPassword::submit() completado exitosamente');
            
        } catch (\Exception $e) {
            Log::error('Error en ResetPassword::submit()');
            Log::error('Mensaje: ' . $e->getMessage());
            Log::error('Stack: ' . $e->getTraceAsString());
            throw $e;
        }
        
        Log::info('=== FIN: ResetPassword::submit() ===');
    }
}
