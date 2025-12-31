<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CustomPasswordBroker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class PasswordResetServiceProvider extends ServiceProvider
{
    /**
     * Register the password broker.
     */
    public function register(): void
    {
        Log::info('PasswordResetServiceProvider::register() iniciado');
    }

    /**
     * Bootstrap the service provider.
     */
    public function boot(): void
    {
        Log::info('PasswordResetServiceProvider::boot() iniciado');

        // Registrar nuestro broker personalizado usando Password::resolver()
        Password::broker('users', function ($app) {
            Log::info('Resolviendo CustomPasswordBroker para el broker "users"');
            
            $config = $app['config']['auth.passwords.users'];
            
            $broker = new CustomPasswordBroker(
                $app['auth.password.tokens'],
                $app['auth']->createUserProvider($config['provider'] ?? 'users'),
                $app['hash'],
                $config
            );
            
            Log::info('✓ CustomPasswordBroker creado exitosamente');
            return $broker;
        });

        Log::info('PasswordResetServiceProvider::boot() completado');
    }
}



