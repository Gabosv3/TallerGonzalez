<?php
namespace App\Providers;

use App\Models\User;
use Filament\Support\Facades\FilamentView;
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

        // Registrar el Observer para Producto
        \App\Models\Producto::observe(\App\Observers\ProductoObserver::class);

        // Valores por defecto de todas las tablas del panel
        \Filament\Tables\Table::configureUsing(fn (\Filament\Tables\Table $table) => $table
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->extremePaginationLinks()
            ->striped()
            ->filtersLayout(\Filament\Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(['default' => 1, 'md' => 2, 'xl' => 4])
            ->filtersTriggerAction(fn ($action) => $action->label('Filtros')->icon('heroicon-m-funnel')->color('gray')));

        \Filament\Tables\Actions\ActionGroup::configureUsing(fn ($group) => $group
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('gray')
            ->tooltip('Acciones'));

        // Aviso de nuevo cliente (se omiten los clientes temporales creados desde la API)
        \App\Models\Cliente::created(function ($cliente) {
            if ($cliente->apellido !== 'Temporal') {
                \App\Services\Notificaciones::clienteNuevo($cliente);
            }
        });

        // Estilos y mensaje de marca del login (sin depender de Vite)
        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): string => collect(['login', 'panel'])
                ->map(fn ($css) => '<link rel="stylesheet" href="' . asset("css/{$css}.css") . '?v=' . @filemtime(public_path("css/{$css}.css")) . '">')
                ->implode(''),
        );

        FilamentView::registerRenderHook(
            'panels::footer',
            fn (): string => '<aside class="login-brand">'
                . '<img src="' . e(\App\Providers\Filament\AdministrativoPanelProvider::logoUrl()) . '" alt="Taller González">'
                . '<div><h2>Taller González</h2><p>Sistema de gestión de inventario, pedidos y facturación.</p></div>'
                . '<small>© ' . date('Y') . ' Taller González</small>'
                . '</aside>',
            scopes: [
                \App\Filament\Pages\Auth\Login::class,
                \App\Filament\Pages\Auth\RequestPasswordReset::class,
                \App\Filament\Pages\Auth\ResetPassword::class,
            ],
        );
    }
}

