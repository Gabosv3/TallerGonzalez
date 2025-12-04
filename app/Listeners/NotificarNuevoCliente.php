<?php

namespace App\Listeners;

use App\Models\Cliente;
use App\Models\User;
use Filament\Notifications\Notification;

class NotificarNuevoCliente
{
    public function handle($event): void
    {
        if (!($event instanceof \Illuminate\Database\Events\ModelCreated)) {
            return;
        }

        if (!($event->model instanceof Cliente)) {
            return;
        }

        $cliente = $event->model;

        // Notificar a usuarios de ventas
        $usuarios = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'vendedor']);
        })->get();

        foreach ($usuarios as $usuario) {
            Notification::make()
                ->title('👤 Nuevo Cliente Registrado')
                ->body("Cliente: {$cliente->nombre}")
                ->icon('heroicon-o-user-plus')
                ->color('info')
                ->actions([
                    \Filament\Notifications\Actions\Action::make('ver')
                        ->button()
                        ->markAsRead()
                        ->url(route('filament.administrativo.resources.clientes.view', $cliente->id)),
                ])
                ->sendToDatabase($usuario);
        }
    }
}
