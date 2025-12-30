<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use App\Services\CustomPasswordBroker;
use Illuminate\Support\Facades\Log;

class PasswordResetServiceProvider extends ServiceProvider
{
    /**
     * Register the password broker.
     */
    public function register(): void
    {
        $this->app->singleton('auth.password.broker', function ($app) {
            Log::info('Registrando CustomPasswordBroker en el contenedor');
            
            // Obtener el manager de password brokers
            $manager = new PasswordBrokerManager($app);

            // Resolver el broker 'users' con nuestro broker personalizado
            $manager->extend('users', function ($app, $config) {
                Log::info('Usando CustomPasswordBroker para el broker "users"');
                
                return new CustomPasswordBroker(
                    $app['auth.password.tokens'],
                    $app['auth']->createUserProvider($config['provider']),
                    $app['hash'],
                    $config
                );
            });

            return $manager;
        });
    }

    /**
     * Bootstrap the service provider.
     */
    public function boot(): void
    {
        Log::info('PasswordResetServiceProvider bootstrapped');
    }
}
