<?php
namespace App\Providers;

use App\Models\User;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Policies\GeneralSettingsPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

use Joaopaulolndev\FilamentGeneralSettings\Models\GeneralSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // No se necesita registrar nada aquí si no tienes servicios adicionales.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar la política para GeneralSetting
        Gate::policy(GeneralSetting::class, GeneralSettingsPolicy::class);

        // Si tienes políticas adicionales, puedes agregarlas aquí de forma similar
        // Gate::policy(AnotherModel::class, AnotherModelPolicy::class);

        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            return str_replace('Models', 'Policies', $modelClass) . 'Policy';
        });

        // Registrar el Observer para Producto
        \App\Models\Producto::observe(\App\Observers\ProductoObserver::class);

        // Este código parece estar relacionado con un hook para tu vista personalizada de inicio de sesión
        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            fn (): string => Blade::render('@vite(\'resources/css/custom-login.css\')'),
        );

        // ===== INTERCEPTOR DE PASSWORD RESET =====
        // Esto intercepta TODOS los intentos de reset de contraseña
        // y se asegura de que el correo se envíe correctamente
        Password::useResetNotifier(function (User $user, string $token) {
            Log::info('=== INTERCEPTOR PASSWORD RESET ACTIVADO ===');
            Log::info("Usuario: {$user->email}");
            Log::info("Token generado: {$token}");
            
            try {
                // Construir la URL de reset
                $url = route('filament.administrativo.auth.password-reset.reset', [
                    'token' => $token,
                    'email' => $user->email,
                ]);
                
                Log::info("URL de reset: {$url}");
                
                // Enviar el correo directamente
                Mail::to($user->email)->send(new ResetPasswordMail($url));
                
                Log::info("✓ Correo enviado exitosamente a: {$user->email}");
                
            } catch (\Exception $e) {
                Log::error("✗ ERROR enviando correo: " . $e->getMessage());
                Log::error("Stack trace: " . $e->getTraceAsString());
                throw $e;
            }
        });
    }
}
