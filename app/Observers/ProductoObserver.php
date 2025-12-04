<?php

namespace App\Observers;

use App\Models\Producto;
use App\Models\User;
use Filament\Notifications\Notification;

class ProductoObserver
{
    /**
     * Notificar cuando el stock es muy bajo
     */
    public function updated(Producto $producto): void
    {
        // Si el stock es crítico (menor que la mitad del mínimo)
        if ($producto->stock_actual < ($producto->stock_minimo / 2) && $producto->stock_actual > 0) {
            $usuarios = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'almacen']);
            })->get();

            foreach ($usuarios as $usuario) {
                Notification::make()
                    ->title('🔴 Stock Crítico')
                    ->body("Producto: {$producto->nombre} - Stock: {$producto->stock_actual} unidades")
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->sendToDatabase($usuario);
            }
        }

        // Si se agotó el stock
        if ($producto->stock_actual === 0 && $producto->wasChanged('stock_actual')) {
            $usuarios = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'almacen', 'vendedor']);
            })->get();

            foreach ($usuarios as $usuario) {
                Notification::make()
                    ->title('❌ Producto Agotado')
                    ->body("Producto: {$producto->nombre} - Sin stock disponible")
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->sendToDatabase($usuario);
            }
        }
    }
}
