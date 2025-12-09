<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';

    protected $fillable = [
        'placa',
        'productos',
        'estado',
        'notas',
    ];

    protected $casts = [
        'productos' => 'array',
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
