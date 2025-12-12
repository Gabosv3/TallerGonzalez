<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoDetalle extends Model
{
    protected $table = 'pedido_detalles';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'aceite_id',
        'producto_nombre',
        'producto_codigo',
        'unidad_medida',
        'cantidad',
        'cantidad_recibida',
        'precio_unitario',
        'subtotal',
        'completado',
        'recibido_at',
        'notas',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'cantidad_recibida' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'completado' => 'boolean',
        'recibido_at' => 'datetime',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

     // Nueva relación con Aceite
    public function aceite(): BelongsTo
    {
        return $this->belongsTo(Aceite::class);
    }

    // Calcular subtotal automáticamente
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->subtotal = $model->cantidad * $model->precio_unitario;
            
            // Actualizar estado de completado
            if ($model->cantidad_recibida >= $model->cantidad) {
                $model->completado = true;
                $model->recibido_at = now();
            }
        });
    }

    // AGREGADO: Accesores útiles para las variantes
    public function getTieneVarianteAttribute(): bool
    {
        return !is_null($this->aceite_id);
    }

    public function getDescripcionVarianteAttribute(): ?string
    {
        if ($this->aceite_id && $this->aceite) {
            return $this->aceite->marca->nombre . ' ' . $this->aceite->viscosidad;
        }
        return null;
    }

    // Accesor para obtener el precio sin IVA (el que se guarda es el sin IVA)
    public function getPrecioSinIvaAttribute(): float
    {
        return (float) $this->precio_unitario;
    }

    // Accesor para obtener el precio con IVA (13%)
    public function getPrecioConIvaAttribute(): float
    {
        return round($this->precio_unitario * 1.13, 2);
    }

    // Accesor para obtener el monto del IVA en este detalle
    public function getMontoIvaAttribute(): float
    {
        return round($this->precio_unitario * 0.13, 2);
    }

    public function getDescripcionCompletaAttribute(): string
    {
        $descripcion = $this->producto_nombre ?? $this->producto->nombre;
        
        if ($this->tiene_variante) {
            $descripcion .= ' - ' . $this->descripcion_variante;
        }
        
        return $descripcion;
    }

}