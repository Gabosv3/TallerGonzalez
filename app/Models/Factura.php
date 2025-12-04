<?php

namespace App\Models;

use App\Events\VentaCreada;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Factura extends Model
{
    use LogsActivity;

    protected $fillable = ['numero_factura', 'fecha', 'cliente', 'total', 'cliente_id', 'estado', 'created_by'];

    protected $dispatchesEvents = [
        'created' => VentaCreada::class,
    ];

    protected static $logAttributes = ['*'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function detalles()
    {
        return $this->hasMany(DetalleFactura::class);
    }

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }

    public function creador()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Restaurar stock de los detalles (entrada)
     */
    public function restoreStock(): void
    {
        $this->loadMissing('detalles.producto');

        foreach ($this->detalles as $detalle) {
            $producto = $detalle->producto;
            if ($producto && $producto->control_stock) {
                // Usar actualizarStock con tipo 'entrada' para revertir la salida
                $producto->actualizarStock((int)$detalle->cantidad, 'entrada');
            }
        }
    }

    /**
     * Realizar descargo de inventario según los detalles (salida)
     */
    public function dischargeStock(): void
    {
        $this->loadMissing('detalles.producto');

        foreach ($this->detalles as $detalle) {
            $producto = $detalle->producto;
            if ($producto && $producto->control_stock) {
                $producto->actualizarStock((int)$detalle->cantidad, 'salida');
            }
        }
    }
}

