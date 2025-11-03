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
}