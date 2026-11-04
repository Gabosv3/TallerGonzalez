<?php

namespace App\Models;

use App\Exceptions\PedidoNoEliminableException;
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
        'descuento_tipo',
        'descuento_valor',
        'descuento_monto',
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
        'descuento_valor' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'impuesto_porcentaje' => 'decimal:2',
        'monto_impuesto' => 'decimal:2',
        'total' => 'decimal:2',
        'confirmado_at' => 'datetime',
        'completado_at' => 'datetime',
        'cancelado_at' => 'datetime',
    ];

    /**
     * Reglas de validación para el modelo
     */
    public static function validationRules(): array
    {
        return [
            'proveedor_id' => 'required|exists:proveedores,id',
            'user_id' => 'nullable|exists:users,id',
            'numero_factura' => 'required|string|max:50|unique:pedidos,numero_factura',
            'fecha_orden' => 'required|date',
            'fecha_esperada' => 'required|date|after_or_equal:fecha_orden',
            'fecha_entrega' => 'nullable|date',
            'estado' => 'required|in:pendiente,confirmado,en_camino,parcial,completado,cancelado',
            'contacto_proveedor' => 'nullable|string|max:100',
            'telefono_proveedor' => 'nullable|string|max:20',
            'subtotal' => 'numeric|min:0',
            'descuento_tipo' => 'nullable|in:monto,porcentaje',
            'descuento_valor' => 'nullable|numeric|min:0',
            'impuesto_porcentaje' => 'numeric|min:0|max:100',
            'monto_impuesto' => 'numeric|min:0',
            'total' => 'numeric|min:0',
            'observaciones' => 'nullable|string',
            'terminos_pago' => 'nullable|string',
            'condiciones_entrega' => 'nullable|string',
        ];
    }

    /**
     * Mensajes de validación en español
     */
    public static function validationMessages(): array
    {
        return [
            'proveedor_id.required' => 'Debes seleccionar un proveedor.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'numero_factura.required' => 'El número de factura es obligatorio.',
            'numero_factura.max' => 'El número de factura no puede exceder 50 caracteres.',
            'numero_factura.unique' => 'Este número de factura ya está registrado.',
            'fecha_orden.required' => 'La fecha de orden es obligatoria.',
            'fecha_orden.date' => 'La fecha de orden debe ser una fecha válida.',
            'fecha_esperada.required' => 'La fecha esperada es obligatoria.',
            'fecha_esperada.date' => 'La fecha esperada debe ser una fecha válida.',
            'fecha_esperada.after_or_equal' => 'La fecha esperada no puede ser anterior a la fecha de orden.',
            'fecha_entrega.date' => 'La fecha de entrega debe ser una fecha válida.',
            'estado.required' => 'El estado del pedido es obligatorio.',
            'estado.in' => 'El estado debe ser uno de: pendiente, confirmado, en camino, parcial, completado, cancelado.',
            'subtotal.numeric' => 'El subtotal debe ser un número.',
            'subtotal.min' => 'El subtotal no puede ser negativo.',
            'impuesto_porcentaje.numeric' => 'El porcentaje de impuesto debe ser un número.',
            'impuesto_porcentaje.min' => 'El porcentaje no puede ser negativo.',
            'impuesto_porcentaje.max' => 'El porcentaje no puede exceder 100.',
            'monto_impuesto.numeric' => 'El monto de impuesto debe ser un número.',
            'monto_impuesto.min' => 'El monto de impuesto no puede ser negativo.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total no puede ser negativo.',
        ];
    }

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
     * Optimizado para hacer menos queries
     */
    public function actualizarStock(): void
    {
        // Cargar todos los detalles con sus relaciones de una vez
        $detalles = $this->detalles()->with(['producto', 'aceite'])->get();
        
        foreach ($detalles as $detalle) {
            $producto = $detalle->producto;
            $cantidad = $detalle->cantidad;
            
            if (!$producto) continue;
            
            // Actualizar stock del producto principal
            $producto->increment('stock_actual', $cantidad);
            
            // Si es un aceite, actualizar también el stock de las variantes específicas
            if ($producto->es_aceite && $detalle->aceite) {
                $detalle->aceite->increment('stock_disponible', $cantidad);
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
        return match ($this->estado) {
            'pendiente' => ['color' => 'warning', 'label' => 'Pendiente'],
            'confirmado' => ['color' => 'info', 'label' => 'Confirmado'],
            'parcial' => ['color' => 'primary', 'label' => 'Parcialmente Entregado'],
            'completado' => ['color' => 'success', 'label' => 'Completado'],
            'cancelado' => ['color' => 'danger', 'label' => 'Cancelado'],
            default => ['color' => 'gray', 'label' => $this->estado],
        };
    }

    /**
     * Cálculo único de totales (lo usan el formulario, el guardado y los reportes).
     * El descuento se aplica antes del impuesto: base = subtotal - descuento; impuesto = base x %.
     *
     * @return array{subtotal: float, descuento_monto: float, base: float, monto_impuesto: float, total: float}
     */
    public static function calcular(float $subtotal, ?string $tipoDescuento, float $valorDescuento, float $impuestoPorcentaje): array
    {
        $subtotal = round(max($subtotal, 0), 2);
        $valorDescuento = max($valorDescuento, 0);

        $descuento = $tipoDescuento === 'porcentaje'
            ? round($subtotal * (min($valorDescuento, 100) / 100), 2)
            : round($valorDescuento, 2);
        $descuento = min($descuento, $subtotal);

        $base = round($subtotal - $descuento, 2);
        $impuesto = round($base * (max($impuestoPorcentaje, 0) / 100), 2);

        return [
            'subtotal' => $subtotal,
            'descuento_monto' => $descuento,
            'base' => $base,
            'monto_impuesto' => $impuesto,
            'total' => round($base + $impuesto, 2),
        ];
    }

    /**
     * Base imponible (subtotal menos descuento).
     */
    public function getBaseImponibleAttribute(): float
    {
        return round((float) $this->subtotal - (float) $this->descuento_monto, 2);
    }

    /**
     * Recalcular y guardar los totales a partir de los detalles guardados.
     */
    public function calcularTotales(): void
    {
        $detalles = $this->detalles()->get();

        $subtotal = $detalles->sum(fn ($detalle) => (float) $detalle->cantidad * (float) $detalle->precio_unitario);

        $totales = self::calcular(
            $subtotal,
            $this->descuento_tipo,
            (float) $this->descuento_valor,
            (float) $this->impuesto_porcentaje,
        );

        $this->update([
            'subtotal' => $totales['subtotal'],
            'descuento_monto' => $totales['descuento_monto'],
            'monto_impuesto' => $totales['monto_impuesto'],
            'total' => $totales['total'],
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
     * Validar que el pedido puede ser eliminado
     */
    public function puedeSerEliminado(): bool
    {
        return !in_array($this->estado, ['completado', 'cancelado']);
    }

    /**
     * Validar que tenga detalles
     */
    public function tieneDetalles(): bool
    {
        return $this->detalles()->count() > 0;
    }

    /**
     * Validar que el total sea válido
     */
    public function totalValido(): bool
    {
        return $this->total > 0;
    }

    /**
     * Validar fechas consistentes
     */
    public function fechasValidas(): bool
    {
        if ($this->fecha_orden && $this->fecha_esperada && $this->fecha_esperada < $this->fecha_orden) {
            return false;
        }

        if ($this->fecha_entrega && $this->fecha_orden && $this->fecha_entrega < $this->fecha_orden) {
            return false;
        }

        return true;
    }

    /**
     * Boot - Manejo de eventos
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pedido) {
            if (empty($pedido->numero_factura)) {
                $hoy = now()->format('Ymd');
                $contadorHoy = Pedido::withTrashed()->whereDate('created_at', now()->toDateString())->count() + 1;
                $pedido->numero_factura = 'PED-' . $hoy . '-' . str_pad($contadorHoy, 4, '0', STR_PAD_LEFT);
            }
        });

        static::deleting(function ($pedido) {
            if (!$pedido->puedeSerEliminado()) {
                throw new PedidoNoEliminableException('No se puede eliminar un pedido completado o cancelado.');
            }
        });

        static::updating(function ($pedido) {
            if ($pedido->isDirty('estado')) {
                $nuevoEstado = $pedido->estado;
                if ($nuevoEstado === 'completado') {
                    $pedido->completado_at = now();
                } elseif ($nuevoEstado === 'confirmado') {
                    $pedido->confirmado_at = now();
                } elseif ($nuevoEstado === 'cancelado') {
                    $pedido->cancelado_at = now();
                }
            }
        });

        static::updated(function ($pedido) {
            if ($pedido->wasChanged('estado') && $pedido->estado === 'completado') {
                // Solo actualizar stock cuando se completa
                $pedido->actualizarStock();
            }
        });
    }
}