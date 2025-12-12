<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';

    protected $fillable = [
        'placa',
        'productos',
        'servicios',
        'estado',
        'notas',
    ];

    protected $casts = [
        'productos' => 'array',
        'servicios' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtener la cantidad de productos registrados
     */
    public function getCantidadProductosAttribute(): int
    {
        return count($this->productos ?? []);
    }

    /**
     * Obtener el total de cantidad de productos
     */
    public function getTotalUnidadesAttribute(): float
    {
        if (!$this->productos) {
            return 0;
        }

        return array_sum(array_column($this->productos, 'cantidad'));
    }

    /**
     * Obtener el total de servicios realizados
     */
    public function getTotalServiciosAttribute(): float
    {
        if (!$this->servicios) {
            return 0;
        }

        $total = 0;
        foreach ($this->servicios as $servicio) {
            $precio = $servicio['servicio_precio'] ?? 0;
            $cantidad = $servicio['servicio_cantidad'] ?? 1;
            $total += $precio * $cantidad;
        }

        return $total;
    }

    /**
     * Obtener el total general (productos + servicios)
     */
    public function getTotalGeneralAttribute(): float
    {
        return $this->total_servicios;
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para búsqueda por placa
     */
    public function scopeBuscarPlaca($query, $placa)
    {
        return $query->where('placa', 'LIKE', "%{$placa}%");
    }
}
