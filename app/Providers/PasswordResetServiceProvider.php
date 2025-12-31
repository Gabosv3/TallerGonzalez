<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use App\Services\CustomPasswordBroker;
use Illuminate\Support\Facades\Log;

class PasswordResetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     */
    public function boot(): void
    {
        Log::info('PasswordResetServiceProvider::boot() iniciado');

        // Extender el manager de password brokers para usar nuestro broker personalizado
        $this->app['auth.password']->extend('users', function ($app, $config) {
            Log::info('Registrando CustomPasswordBroker para el broker "users"');
            
            $broker = new CustomPasswordBroker(
                $app['auth.password.tokens'],
                $app['auth']->createUserProvider($config['provider'] ?? 'users'),
                $app['hash'],
                $config
            );
            
            Log::info('CustomPasswordBroker registrado exitosamente');
            return $broker;
        });

        Log::info('PasswordResetServiceProvider::boot() completado');
    }
}

