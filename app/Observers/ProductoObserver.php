<?php

namespace App\Observers;

use App\Models\Producto;
use App\Services\Notificaciones;

class ProductoObserver
{
    /**
     * Notifica una sola vez cuando el stock cruza el mínimo o se agota.
     */
    public function updated(Producto $producto): void
    {
        if (! $producto->wasChanged('stock_actual')) {
            return;
        }

        $antes = (float) $producto->getOriginal('stock_actual');
        $ahora = (float) $producto->stock_actual;
        $minimo = (float) $producto->stock_minimo;

        if ($ahora <= 0 && $antes > 0) {
            Notificaciones::stockBajo($producto);
        } elseif ($minimo > 0 && $ahora <= $minimo && $antes > $minimo) {
            Notificaciones::stockBajo($producto);
        }
    }
}
