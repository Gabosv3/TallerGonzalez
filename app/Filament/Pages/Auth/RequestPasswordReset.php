<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Illuminate\Support\Facades\Log;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    /**
     * El CustomPasswordBroker en AppServiceProvider maneja todo el envío de correos.
     * Esta clase solo deja que el flujo de Filament ocurra normalmente.
     */
    public function submit(): void
    {
        Log::info('=== RequestPasswordReset::submit() iniciado ===');
        Log::info('El CustomPasswordBroker manejará el envío del correo automáticamente');
        
        try {
            // Llamar al submit padre que usa Password::sendResetLink()
            // Nuestro CustomPasswordBroker interceptará esa llamada
            parent::submit();
            Log::info('✓ Flujo completado exitosamente');
        } catch (\Exception $e) {
            Log::error('✗ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}

