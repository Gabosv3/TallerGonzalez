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
     * Calcular y actualizar los totales del pedido
     * Optimizado para evitar queries extras
     */
    public function calcularTotales(): void
    {
        // Si los detalles ya están cargados en memoria, usarlos
        // De lo contrario, cargar solo lo necesario
        $detalles = $this->relationLoaded('detalles') 
            ? $this->detalles 
            : $this->detalles()->get();
        
        $subtotal = $detalles->sum(function ($detalle) {
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