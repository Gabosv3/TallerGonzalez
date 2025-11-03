<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aceite extends Model
{
    

    protected $table = 'aceites';

    protected $fillable = [
        'producto_id',
        'marca_id',
        'modelo',
        'viscosidad',
        'tipo_aceite_id',
        'capacidad_ml',
        'unidad_medida',
        'presentacion',
        'norma_api',
        'norma_acea',
        'viscosidad_sae',
        'punto_ignicion',
        'punto_fluidez',
        'aplicaciones',
        'compatibilidad',
        'stock_disponible',
        'stock_minimo',
        'stock_maximo',
        'activo',
    ];

    protected $casts = [
        'capacidad_ml' => 'decimal:2',
        'punto_ignicion' => 'decimal:1',
        'punto_fluidez' => 'decimal:1',
        'aplicaciones' => 'array',
        'stock_disponible' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'activo' => 'boolean',
    ];

    // Relación con producto principal
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Relación con marca
    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    // Relación con tipo de aceite
    public function tipoAceite(): BelongsTo
    {
        return $this->belongsTo(TipoAceite::class);
    }

    // Helper para mostrar capacidad formateada
    public function getCapacidadFormateadaAttribute(): string
    {
        if ($this->capacidad_ml >= 1000) {
            return ($this->capacidad_ml / 1000) . ' L';
        }
        return $this->capacidad_ml . ' ml';
    }

    // Helper para nombre completo
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->marca->nombre} {$this->viscosidad} {$this->capacidad_formateada}";
    }

    // Scopes para búsquedas
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorMarca($query, $marcaId)
    {
        return $query->where('marca_id', $marcaId);
    }

    public function scopePorViscosidad($query, $viscosidad)
    {
        return $query->where('viscosidad', $viscosidad);
    }

    public function scopePorTipo($query, $tipoId)
    {
        return $query->where('tipo_aceite_id', $tipoId);
    }

    public function scopeConStock($query)
    {
        return $query->where('stock_disponible', '>', 0);
    }

    // Verificar si está bajo stock mínimo
    public function getBajoStockAttribute(): bool
    {
        return $this->stock_disponible <= $this->stock_minimo;
    }
}