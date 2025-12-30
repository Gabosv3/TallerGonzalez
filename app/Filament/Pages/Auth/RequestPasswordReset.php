<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    public function register(): void
    {
        \Illuminate\Support\Facades\Log::info('RequestPasswordReset::register() llamado');
        parent::register();
    }
}
