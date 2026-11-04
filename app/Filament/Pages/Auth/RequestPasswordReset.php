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
    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Recuperar contraseña';
    }

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
            
            if ($status === Password::RESET_THROTTLED) {
                Log::warning("Solicitud limitada para: {$email}");
                Notification::make()
                    ->title('Espera un momento')
                    ->body('Ya te enviamos un enlace hace poco. Revisa tu correo o vuelve a intentarlo en un minuto.')
                    ->warning()
                    ->send();

                return;
            }

            // Mismo mensaje exista o no el correo, para no revelar qué cuentas están registradas
            Notification::make()
                ->title('Revisa tu correo')
                ->body('Si el correo está registrado, te enviamos un enlace para crear una nueva contraseña. Es válido por 60 minutos.')
                ->success()
                ->send();

            $this->redirect(route('filament.administrativo.auth.login'));

        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            Notification::make()
                ->title('Error')
                ->body('Ocurrió un error al procesar tu solicitud. Por favor intenta de nuevo.')
                ->danger()
                ->send();
        }
    }
}


