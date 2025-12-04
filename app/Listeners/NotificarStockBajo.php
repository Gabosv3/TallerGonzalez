<?php

namespace App\Listeners;

use App\Events\VentaCreada;
use App\Models\User;
use Filament\Notifications\Notification;

class NotificarStockBajo
{
    public function handle(VentaCreada $event): void
    {
        $factura = $event->factura;
        
        // Verificar si hay productos con stock bajo después de la venta
        foreach ($factura->detalles as $detalle) {
            $producto = $detalle->producto;
            
            if ($producto && $producto->stock_actual < $producto->stock_minimo) {
                // Notificar a usuarios de almacén
                $usuarios = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'almacen']);
                })->get();

                foreach ($usuarios as $usuario) {
                    Notification::make()
                        ->title('⚠️ Stock Bajo')
                        ->body("Producto: {$producto->nombre} - Stock actual: {$producto->stock_actual}")
                        ->icon('heroicon-o-exclamation')
                        ->color('warning')
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('ver')
                                ->button()
                                ->markAsRead()
                                ->url(route('filament.administrativo.resources.productos.view', $producto->id)),
                        ])
                        ->sendToDatabase($usuario);
                }
            }
        }
    }
}
