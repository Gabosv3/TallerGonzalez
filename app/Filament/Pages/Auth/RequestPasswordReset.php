<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->placeholder('tu@correo.com')
                    ->helperText('Ingresa el correo asociado a tu cuenta'),
            ]);
    }

    /**
     * El CustomPasswordBroker en AppServiceProvider maneja todo el envío de correos.
     * Esta clase solo se asegura de que el flujo de Filament ocurra normalmente.
     */
    public function submit(): void
    {
        Log::info('=== RequestPasswordReset::submit() iniciado ===');
        Log::info('El CustomPasswordBroker manejará el envío del correo automáticamente');
        
        try {
            $data = $this->form->getState();
            $email = $data['email'] ?? null;
            
            Log::info("Solicitando reset para: {$email}");
            
            // El Password::sendResetLink() ahora disparará nuestro CustomPasswordBroker
            $status = Password::sendResetLink(['email' => $email]);
            
            Log::info("Estado del reset: {$status}");
            
            if ($status === Password::RESET_LINK_SENT) {
                Log::info("✓ Enlace enviado correctamente");
                Notification::make()
                    ->title('✓ Enlace Enviado')
                    ->body('Se ha enviado un enlace de restablecimiento a tu correo electrónico. Por favor revisa tu bandeja de entrada.')
                    ->success()
                    ->send();
                    
                $this->redirect(route('filament.administrativo.auth.login'));
            } else {
                Log::warning("Fallo al enviar enlace: {$status}");
                Notification::make()
                    ->title('⏱️ Intenta Más Tarde')
                    ->body('Has solicitado demasiados enlaces. Por favor espera un tiempo antes de intentar nuevamente.')
                    ->warning()
                    ->send();
            }
            
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            Notification::make()
                ->title('❌ Error')
                ->body('Ocurrió un error al procesar tu solicitud. Por favor intenta de nuevo.')
                ->danger()
                ->send();
        }
    }
}


