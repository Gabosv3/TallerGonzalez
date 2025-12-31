<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\ResetPassword as BaseResetPassword;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ResetPassword extends BaseResetPassword
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                    
                TextInput::make('password')
                    ->label('Nueva Contraseña')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/')
                    ->validationMessages([
                        'regex' => 'La contraseña debe contener minúscula, mayúscula, número y carácter especial (@$!%*?&).',
                    ])
                    ->helperText('Mínimo 8 caracteres con mayúscula, minúscula, número y símbolo')
                    ->revealable(),
                    
                TextInput::make('password_confirmation')
                    ->label('Confirmar Contraseña')
                    ->password()
                    ->required()
                    ->same('password')
                    ->revealable()
                    ->validationMessages([
                        'same' => 'Las contraseñas no coinciden.',
                    ]),
            ]);
    }

    public function submit(): void
    {
        Log::info('=== ResetPassword::submit() iniciado ===');
        
        try {
            parent::submit();
            
            Notification::make()
                ->title('✓ Contraseña Actualizada')
                ->body('Tu contraseña ha sido restablecida correctamente. Ahora puedes iniciar sesión.')
                ->success()
                ->send();
            
            Log::info('✓ Contraseña restablecida exitosamente');
            
        } catch (ValidationException $e) {
            Log::warning('Error de validación: ' . json_encode($e->errors()));
            Notification::make()
                ->title('❌ Error de Validación')
                ->body('Por favor verifica los datos ingresados.')
                ->danger()
                ->send();
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            Notification::make()
                ->title('❌ Error')
                ->body('Ocurrió un error al restablecer tu contraseña. Por favor intenta de nuevo.')
                ->danger()
                ->send();
            throw $e;
        }
    }
}

