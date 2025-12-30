<?php

namespace App\Providers\Filament;

use Backstage\TwoFactorAuth\TwoFactorAuthPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
    public function panel(Panel $panel): Panel
    {
        $settings =  GeneralSetting::first(); // Obtén la primera configuración de la base de datos


        return $panel
            ->default()
            ->id('administrativo')
            ->path('administrativo')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->passwordReset(
                \App\Filament\Pages\Auth\RequestPasswordReset::class,
                \App\Filament\Pages\Auth\ResetPassword::class
            )


            ->colors([
                'primary' => $settings && $settings->theme_color ? $settings->theme_color : '#FFA500',  // Si es null, se pone el color predeterminado
            ])
            ->brandLogo(asset($settings && $settings->site_logo ? 'storage/' . $settings->site_logo : 'assets/img/tallergonzalez.png'))
            ->brandLogoHeight('3.5rem')
            ->brandName($settings && $settings->site_name ? $settings->site_name : 'No se encontró')  // Si el nombre del sitio es null, se pone 'No se encontró'
            ->darkModeBrandLogo(asset($settings && $settings->site_logo ? 'storage/' . $settings->site_logo : 'assets/img/tallergonzalez.png'))
            ->discoverResources(app_path('Filament/Resources'), 'App\\Filament\\Resources')
            ->discoverPages(app_path('Filament/Pages'), 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(app_path('Filament/Widgets'), 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\PedidosPorMes::class,
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
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->plugins([
                FilamentShieldPlugin::make(),
                // Asegúrate de tener este plugin instalado
                FilamentGeneralSettingsPlugin::make()
                    ->setIcon('heroicon-o-cog')
                    ->setNavigationGroup('Configuración del Sistema')
                    ->setTitle('Configuraciones')
                    ->setNavigationLabel('Configuraciones Generales'),

                FilamentEditProfilePlugin::make()
                    ->slug('mi-perfil')
                    ->setTitle('Mi Perfil')
                    ->setNavigationLabel('Mi Perfil')
                    ->setNavigationGroup('Configuración del Sistema')
                    ->setIcon('heroicon-o-user'),
                TwoFactorAuthPlugin::make(),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPage(Backups::class)
                    ->authorize(fn() => true),
            ])
            ->registration(null) // Deshabilitar registro explícitamente
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
