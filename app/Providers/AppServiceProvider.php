<?php
namespace App\Providers;

use App\Models\User;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Policies\GeneralSettingsPolicy;
use Illuminate\Support\Facades\Gate;

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

        // Este código parece estar relacionado con un hook para tu vista personalizada de inicio de sesión
        FilamentView::registerRenderHook(
            'panels::auth.login.form.after',
            fn (): string => Blade::render('@vite(\'resources/css/custom-login.css\')'),
        );
    }
}
