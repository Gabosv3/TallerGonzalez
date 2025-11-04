<?php

namespace App\Providers\Filament;

use App\Models\Setting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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

class AdministrativoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings =  GeneralSetting::first(); // Obtén la primera configuración de la base de datos


        return $panel
            ->default()
            ->id('administrativo')
            ->path('administrativo')
            ->login()

            ->colors([
                'primary' => $settings && $settings->theme_color ? $settings->theme_color : '#FFA500',  // Si es null, se pone el color predeterminado
            ])
            ->brandLogo(asset($settings && $settings->site_logo ? 'storage/' . $settings->site_logo : 'assets/img/tallergonzalez.png'))  // Si el logo es null, se usa el logo predeterminado
            ->brandLogoHeight('3.5rem')
            ->brandName($settings && $settings->site_name ? $settings->site_name : 'No se encontró')  // Si el nombre del sitio es null, se pone 'No se encontró'
            ->darkModeBrandLogo(asset($settings && $settings->site_logo ? 'storage/' . $settings->site_logo : 'assets/img/tallergonzalez.png'))  // Si el logo es null, se usa el logo predeterminado
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
               
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
                    ->setIcon('heroicon-o-user')


            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
