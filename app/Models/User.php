<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use MixCode\FilamentMulti2fa\Enums\TwoFactorAuthType;
use MixCode\FilamentMulti2fa\Traits\UsingTwoFA;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable,HasRoles,SoftDeletes,TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'avatar_url',
        'deleted_at',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

     protected $guarded = [
        'two_factor_type',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_sent_at',
        'two_factor_expires_at',
        'two_factor_confirmed_at',
    ];

    protected $casts = [
        'two_factor_type' => TwoFactorAuthType::class,
        'two_factor_sent_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Reglas de validación para el modelo
     */
    public static function validationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'avatar_url' => 'nullable|url|max:500',
        ];
    }

    /**
     * Mensajes de validación en español
     */
    public static function validationMessages(): array
    {
        return [
            'name.required' => 'El nombre de usuario es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo debe ser una dirección válida.',
            'email.max' => 'El correo no puede exceder 255 caracteres.',
            'email.unique' => 'Este correo ya está registrado en el sistema.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'avatar_url.url' => 'La URL del avatar debe ser válida.',
            'avatar_url.max' => 'La URL no puede exceder 500 caracteres.',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }


    public function redirectAfterVerifyUrl(): ?string
    {
        return route('filament.administrativo.pages.dashboard');
    }

    /**
     * Enviar la notificación de restablecimiento de contraseña.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        \Illuminate\Support\Facades\Log::info('Intentando enviar correo de restablecimiento a: ' . $this->email);

        $url = route('filament.administrativo.auth.password-reset.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($this->email)->send(new \App\Mail\ResetPasswordMail($url));
            \Illuminate\Support\Facades\Log::info('Correo enviado (o encolado) exitosamente.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error enviando correo: ' . $e->getMessage());
        }
    }
}
