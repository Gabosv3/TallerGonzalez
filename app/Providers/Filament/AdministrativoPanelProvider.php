<?php

namespace App\Providers\Filament;

use Backstage\TwoFactorAuth\TwoFactorAuthPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentGeneralSettings\FilamentGeneralSettingsPlugin;
use Joaopaulolndev\FilamentGeneralSettings\Models\GeneralSetting;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups;

class AdministrativoPanelProvider extends PanelProvider
{
    /**
     * Usa el logo configurado solo si el archivo existe; si no, el logo del repositorio.
     */
    public static function logoUrl(?GeneralSetting $settings = null): string
    {
        $settings ??= GeneralSetting::first();

        // Se comprueba en el disco público (no en public/storage), para que funcione aunque falte el enlace simbólico
        $disco = \Illuminate\Support\Facades\Storage::disk('public');

        if ($settings && $settings->site_logo && $disco->exists($settings->site_logo)) {
            // La versión evita que el navegador muestre el logo anterior: el archivo siempre se llama igual
            return asset('storage/' . $settings->site_logo) . '?v=' . $disco->lastModified($settings->site_logo);
        }

        return asset('assets/img/tallergonzalez.png');
    }

    public function panel(Panel $panel): Panel
    {
        $settings =  GeneralSetting::first(); // Obtén la primera configuración de la base de datos

        $logoUrl = static::logoUrl($settings);
        $siteName = $settings && $settings->site_name ? $settings->site_name : 'Taller González';
        $brand = new \Illuminate\Support\HtmlString(view('filament.brand', ['logo' => $logoUrl, 'name' => $siteName])->render());

        return $panel
            ->default()
            ->id('administrativo')
            ->path('administrativo')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->passwordReset(
                \App\Filament\Pages\Auth\RequestPasswordReset::class,
                \App\Filament\Pages\Auth\ResetPassword::class
            )
            ->authPasswordBroker('users')
            ->emailVerification()


            ->colors([
                'blue' => \Filament\Support\Colors\Color::Blue,
                'green' => \Filament\Support\Colors\Color::Green,
                'orange' => \Filament\Support\Colors\Color::Orange,
                'purple' => \Filament\Support\Colors\Color::Purple,
                'primary' => $settings && $settings->theme_color ? $settings->theme_color : '#FFA500',  // Si es null, se pone el color predeterminado
            ])
            ->brandLogo($brand)
            ->brandLogoHeight('2.75rem')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->navigationGroups([
                NavigationGroup::make('Inventario'),
                NavigationGroup::make('Ventas'),
                NavigationGroup::make('Compras'),
                NavigationGroup::make('Administración')->collapsed(),
            ])
            ->brandName($siteName)
            ->darkModeBrandLogo($brand)
            ->discoverResources(app_path('Filament/Resources'), 'App\\Filament\\Resources')
            ->discoverPages(app_path('Filament/Pages'), 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(app_path('Filament/Widgets'), 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\Bienvenida::class,
                \App\Filament\Widgets\KpiCards::class,
                \App\Filament\Widgets\VentasPorMes::class,
                \App\Filament\Widgets\PedidosPorMes::class,
                \App\Filament\Widgets\ProductosStockBajo::class,
                \App\Filament\Widgets\Top10ProductosMes::class,
                \App\Filament\Widgets\Top10ClientesMes::class,
                \App\Filament\Widgets\ProductosPorMarca::class,
                \App\Filament\Widgets\ProductosMasVendidosTabla::class,
                \App\Filament\Widgets\ClientesMasCompranTabla::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Mi perfil')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn (): string => url('administrativo/mi-perfil')),
            ])
            ->databaseNotifications(isLazy: false)
            ->databaseNotificationsPolling('30s')
            ->globalSearchKeyBindings(['ctrl+k', 'command+k'])
            ->globalSearchFieldKeyBindingSuffix()
            ->globalSearchDebounce('350ms')
            ->plugins([
                FilamentShieldPlugin::make(),
                // Asegúrate de tener este plugin instalado
                FilamentGeneralSettingsPlugin::make()
                    ->setIcon('heroicon-o-cog')
                    ->setNavigationGroup('Administración')
                    ->setSort(30)
                    ->setTitle('Configuración general')
                    ->setNavigationLabel('Configuración general'),

                FilamentEditProfilePlugin::make()
                    ->slug('mi-perfil')
                    ->setTitle('Mi Perfil')
                    ->setNavigationLabel('Mi Perfil')
                    ->shouldRegisterNavigation(false)
                    ->setIcon('heroicon-o-user'),
                TwoFactorAuthPlugin::make(),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPage(Backups::class)
                    ->authorize(fn() => auth()->user()?->hasRole('super_admin') ?? false),
            ])
            ->registration(null) // Deshabilitar registro explícitamente
            ->authMiddleware([
                Authenticate::class,
            ]);

            
    }
}
