<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CustomPasswordBroker;
use Illuminate\Support\Facades\Log;

class PasswordResetServiceProvider extends ServiceProvider
{
    /**
     * Register the password broker.
     */
    public function register(): void
    {
        Log::info('PasswordResetServiceProvider::register() iniciado');

        // Extender el manager de password brokers para usar nuestro broker personalizado
        $this->app->extend('auth.password', function ($manager, $app) {
            Log::info('Extendiendo auth.password manager para registrar CustomPasswordBroker');
            
            $manager->extend('users', function ($app, $config) {
                Log::info('Creando instancia de CustomPasswordBroker');
                
                return new CustomPasswordBroker(
                    $app['auth.password.tokens'],
                    $app['auth']->createUserProvider($config['provider'] ?? 'users'),
                    $app['hash'],
                    $config
                );
            });
            
            return $manager;
        });

        Log::info('PasswordResetServiceProvider::register() completado');
    }

    /**
     * Bootstrap the service provider.
     */
    public function boot(): void
    {
        Log::info('PasswordResetServiceProvider::boot() ejecutado');
    }
}


