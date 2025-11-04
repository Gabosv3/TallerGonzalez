<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use SoftDeletes;

    protected $table = 'pedidos';

    protected $fillable = [
        'proveedor_id',
        'user_id',
        'numero_factura',
        'fecha_orden',
        'fecha_esperada',
        'fecha_entrega',
        'estado',
        'contacto_proveedor',
        'telefono_proveedor',
        'subtotal',
        'impuesto_porcentaje',
        'monto_impuesto',
        'total',
        'observaciones',
        'terminos_pago',
        'condiciones_entrega',
        'confirmado_at',
        'completado_at',
        'cancelado_at',
    ];

    protected $casts = [
        'fecha_orden' => 'date',
        'fecha_esperada' => 'date',
        'fecha_entrega' => 'date',
        'subtotal' => 'decimal:2',
        'impuesto_porcentaje' => 'decimal:2',
        'monto_impuesto' => 'decimal:2',
        'total' => 'decimal:2',
        'confirmado_at' => 'datetime',
        'completado_at' => 'datetime',
        'cancelado_at' => 'datetime',
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class);
    }

    /*public function historial(): HasMany
    {
        return $this->hasMany(PedidoHistorial::class);
    }*/

    // Scopes útiles
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeActivos($query)
    {
        return $query->whereNotIn('estado', ['completado', 'cancelado']);
    }

    public function scopeDelMes($query)
    {
        return $query->whereMonth('fecha_orden', now()->month);
    }

    /**
     * Método para actualizar stock cuando el pedido se completa
     * Ahora maneja variantes individuales en lugar de agrupación
     */
    public function actualizarStock(): void
    {
        foreach ($this->detalles as $detalle) {
            $producto = $detalle->producto;
            $cantidad = $detalle->cantidad;
            
            if ($producto) {
                // Actualizar stock del producto principal
                $producto->increment('stock_actual', $cantidad);
                
                // Si es un aceite, actualizar también el stock de las variantes específicas
                if ($producto->es_aceite) {
                    // Obtener la variante específica del detalle del pedido
                    $aceiteVariante = $detalle->aceite;
                    
                    if ($aceiteVariante) {
                        // Actualizar stock de la variante específica
                        $aceiteVariante->increment('stock_disponible', $cantidad);
                    } else {
                        // Si no hay variante específica, buscar la variante principal
                        $variantePrincipal = $producto->variante_principal;
                        if ($variantePrincipal) {
                            $variantePrincipal->increment('stock_disponible', $cantidad);
                        }
                    }
                }
            }
        }
    }

    /**
     * Obtener el total de items en el pedido
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->detalles->sum('cantidad');
    }

    /**
     * Obtener el número de productos diferentes en el pedido
     */
    public function getNumeroProductosAttribute(): int
    {
        return $this->detalles->count();
    }

    /**
     * Verificar si el pedido puede ser confirmado
     */
    public function getPuedeConfirmarAttribute(): bool
    {
        return in_array($this->estado, ['pendiente', 'confirmado']);
    }

    /**
     * Verificar si el pedido puede ser completado
     */
    public function getPuedeCompletarAttribute(): bool
    {
        return in_array($this->estado, ['confirmado', 'parcial']);
    }

    /**
     * Verificar si el pedido puede ser cancelado
     */
    public function getPuedeCancelarAttribute(): bool
    {
        return !in_array($this->estado, ['completado', 'cancelado']);
    }

    /**
     * Obtener el estado con color para mostrar en la UI
     */
    public function getEstadoConColorAttribute(): array
    {
        return match($this->estado) {
            'pendiente' => ['color' => 'warning', 'label' => 'Pendiente'],
            'confirmado' => ['color' => 'info', 'label' => 'Confirmado'],
            'parcial' => ['color' => 'primary', 'label' => 'Parcialmente Entregado'],
            'completado' => ['color' => 'success', 'label' => 'Completado'],
            'cancelado' => ['color' => 'danger', 'label' => 'Cancelado'],
            default => ['color' => 'gray', 'label' => $this->estado],
        };
    }

    /**
     * Calcular y actualizar los totales del pedido
     */
    public function calcularTotales(): void
    {
        $subtotal = $this->detalles->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->precio_unitario;
        });

        $montoImpuesto = $subtotal * ($this->impuesto_porcentaje / 100);
        $total = $subtotal + $montoImpuesto;

        $this->update([
            'subtotal' => $subtotal,
            'monto_impuesto' => $montoImpuesto,
            'total' => $total,
        ]);
    }

    /**
     * Agregar un producto al pedido
     */
    public function agregarProducto(Producto $producto, int $cantidad, float $precioUnitario, ?Aceite $variante = null): PedidoDetalle
    {
        $detalle = $this->detalles()->create([
            'producto_id' => $producto->id,
            'aceite_id' => $variante?->id,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $cantidad * $precioUnitario,
        ]);

        $this->calcularTotales();

        return $detalle;
    }

    /**
     * Boot method para manejar eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Generar número de factura automáticamente si no se proporciona
        static::creating(function ($pedido) {
            if (empty($pedido->numero_factura)) {
                $pedido->numero_factura = 'PED-' . date('Ymd') . '-' . str_pad(Pedido::withTrashed()->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });

        static::updated(function ($pedido) {
            // Actualizar stock cuando el estado cambia a "completado"
            if ($pedido->wasChanged('estado') && $pedido->estado === 'completado') {
                $pedido->actualizarStock();
                $pedido->completado_at = now();
                $pedido->saveQuietly(); // Guardar sin disparar eventos nuevamente
            }

            // Actualizar confirmado_at cuando el estado cambia a "confirmado"
            if ($pedido->wasChanged('estado') && $pedido->estado === 'confirmado') {
                $pedido->confirmado_at = now();
                $pedido->saveQuietly();
            }

            // Actualizar cancelado_at cuando el estado cambia a "cancelado"
            if ($pedido->wasChanged('estado') && $pedido->estado === 'cancelado') {
                $pedido->cancelado_at = now();
                $pedido->saveQuietly();
            }

            // Recalcular totales cuando cambien los detalles
            if ($pedido->wasChanged(['subtotal', 'impuesto_porcentaje'])) {
                $pedido->calcularTotales();
            }
        });

        // Recalcular totales cuando se crea, actualiza o elimina un detalle
        static::saved(function ($pedido) {
            if ($pedido->wasChanged() && !$pedido->wasChanged(['subtotal', 'monto_impuesto', 'total'])) {
                $pedido->calcularTotales();
            }
        });
    }
}