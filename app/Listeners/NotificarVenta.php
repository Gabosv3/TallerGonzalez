<?php

namespace App\Listeners;

use App\Events\VentaCreada;
use App\Models\User;
use Filament\Notifications\Notification;

class NotificarVenta
{
    public function handle(VentaCreada $event): void
    {
        $factura = $event->factura;
        
        // Notificar a todos los usuarios con rol de admin
        $usuarios = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        foreach ($usuarios as $usuario) {
            $clienteNombre = $factura->cliente ? $factura->cliente->nombre : 'N/A';
            
            Notification::make()
                ->title('🛒 Nueva Venta Registrada')
                ->body("Cliente: {$clienteNombre}")
                ->icon('heroicon-o-shopping-cart')
                ->color('success')
                ->actions([
                    \Filament\Notifications\Actions\Action::make('ver')
                        ->button()
                        ->markAsRead()
                        ->url(route('filament.administrativo.resources.facturas.view', $factura->id)),
                ])
                ->sendToDatabase($usuario);
        }
    }
}
