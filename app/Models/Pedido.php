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
}