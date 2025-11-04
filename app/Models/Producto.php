<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        // Datos básicos
        'codigo',
        'nombre',
        'descripcion',
        'marca_id',
        'categoria_id',
        'unidad_medida',

        // Tipo de producto
        'tipo_producto_id',

        // Precios
        'precio_compra',
        'precio_venta',
        'precio_minimo',

        // Inventario
        'stock_actual',
        'stock_minimo',
        'stock_maximo',

        // Control
        'activo',
        'control_stock',
        'especificaciones_generales'
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'precio_minimo' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'control_stock' => 'boolean',
        'activo' => 'boolean',
    ];

    // Relación con categoría
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    // Relación con marca
    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    // Relación con tipo de producto
    public function tipoProducto(): BelongsTo
    {
        return $this->belongsTo(TipoProducto::class);
    }

    // Relación con aceites (variantes)
    public function aceites(): HasMany
    {
        return $this->hasMany(Aceite::class, 'producto_id');
    }

    // Helper para saber si es producto normal
    public function getEsNormalAttribute(): bool
    {
        return optional($this->tipoProducto)->nombre === 'normal';
    }

    // Helper para saber si es aceite
    public function getEsAceiteAttribute(): bool
    {
        return optional($this->tipoProducto)->nombre === 'aceite';
    }

    // Helper para obtener nombre completo con tipo
    public function getNombreCompletoAttribute(): string
    {
        $tipo = $this->tipoProducto ? " ({$this->tipoProducto->nombre})" : '';
        
        // Si es aceite y tiene variantes, mostrar información de variantes
        if ($this->es_aceite && $this->tiene_variantes) {
            $totalVariantes = $this->aceites->count();
            return "{$this->nombre} [{$totalVariantes} variantes]" . $tipo;
        }
        
        return $this->nombre . $tipo;
    }

    // Helper para estado de stock
    public function getEstadoStockAttribute(): string
    {
        if ($this->stock_actual <= 0) {
            return 'agotado';
        } elseif ($this->stock_actual <= $this->stock_minimo) {
            return 'bajo';
        } else {
            return 'disponible';
        }
    }

    // Helper para verificar si está bajo stock
    public function getBajoStockAttribute(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    // Helper para saber si tiene variantes (múltiples aceites)
    public function getTieneVariantesAttribute(): bool
    {
        return $this->es_aceite && $this->aceites->count() > 1;
    }

    // Helper para obtener stock total de todas las variantes
    public function getStockTotalVariantesAttribute(): int
    {
        if (!$this->es_aceite) {
            return $this->stock_actual;
        }

        return $this->aceites->sum('stock_disponible');
    }

    // Helper para obtener información de variantes
    public function getInfoVariantesAttribute()
    {
        if (!$this->es_aceite) {
            return collect();
        }

        return $this->aceites()
            ->with(['marca', 'tipoAceite'])
            ->get()
            ->map(function ($aceite) {
                return [
                    'id' => $aceite->id,
                    'marca' => $aceite->marca->nombre ?? 'N/A',
                    'viscosidad' => $aceite->viscosidad,
                    'tipo_aceite' => $aceite->tipoAceite->nombre ?? 'N/A',
                    'capacidad' => $aceite->capacidad_formateada,
                    'presentacion' => $aceite->presentacion,
                    'stock_disponible' => $aceite->stock_disponible,
                    'precio_venta' => $this->precio_venta, // Usa el precio del producto principal
                    'activo' => $aceite->activo,
                    'especificaciones' => [
                        'norma_api' => $aceite->norma_api,
                        'norma_acea' => $aceite->norma_acea,
                        'viscosidad_sae' => $aceite->viscosidad_sae,
                    ]
                ];
            });
    }

    // Control de stock unificado
    public function actualizarStock(int $cantidad, string $tipo = 'entrada'): void
    {
        if (!$this->control_stock) return;

        $nuevoStock = $tipo === 'entrada'
            ? $this->stock_actual + $cantidad
            : $this->stock_actual - $cantidad;

        if ($nuevoStock < 0) {
            throw new Exception("Stock insuficiente para el producto: {$this->nombre}");
        }

        $this->update(['stock_actual' => $nuevoStock]);

        $this->verificarAlertaStock();
    }

    private function verificarAlertaStock(): void
    {
        if ($this->bajo_stock) {
            Log::warning("Stock bajo para producto: {$this->nombre} - Stock actual: {$this->stock_actual}");
        }
    }

    // Scopes para búsquedas
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConStock($query)
    {
        return $query->where('stock_actual', '>', 0);
    }

    public function scopeBajoStock($query)
    {
        return $query->whereRaw('stock_actual <= stock_minimo');
    }

    public function scopePorTipo($query, $tipoProductoId)
    {
        return $query->where('tipo_producto_id', $tipoProductoId);
    }

    public function scopeAceites($query)
    {
        return $query->whereHas('tipoProducto', function ($q) {
            $q->where('nombre', 'aceite');
        });
    }

    public function scopeNormales($query)
    {
        return $query->whereHas('tipoProducto', function ($q) {
            $q->where('nombre', 'normal');
        });
    }

    public function scopePorMarca($query, $marcaId)
    {
        return $query->where('marca_id', $marcaId);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    // Scope para productos con variantes
    public function scopeConVariantes($query)
    {
        return $query->whereHas('aceites', function ($q) {
            $q->groupBy('producto_id')
              ->havingRaw('COUNT(*) > 1');
        });
    }

    // Método estático para crear producto con tipo específico
    public static function crearConTipo(array $datosGenerales, array $datosEspecificos = [])
    {
        return DB::transaction(function () use ($datosGenerales, $datosEspecificos) {
            // Crear producto base
            $producto = self::create($datosGenerales);

            // Crear producto específico según tipo
            $tipoProducto = TipoProducto::find($datosGenerales['tipo_producto_id']);

            if ($tipoProducto && $tipoProducto->requiere_especificaciones) {
                switch ($tipoProducto->nombre) {
                    case 'aceite':
                        $especifico = Aceite::create(array_merge(
                            ['producto_id' => $producto->id],
                            $datosEspecificos
                        ));
                        break;

                        // Agregar más casos para otros tipos aquí
                }
            }

            return $producto->load(['tipoProducto', 'aceites', 'marca', 'categoria']);
        });
    }

    // Método para agregar una variante de aceite
    public function agregarVarianteAceite(array $datosVariante): Aceite
    {
        if (!$this->es_aceite) {
            throw new Exception("Solo los productos de tipo aceite pueden tener variantes.");
        }

        return Aceite::create(array_merge(
            ['producto_id' => $this->id],
            $datosVariante
        ));
    }

    // Método para obtener la variante principal (primera variante)
    public function getVariantePrincipalAttribute(): ?Aceite
    {
        if (!$this->es_aceite) {
            return null;
        }

        return $this->aceites->first();
    }

    // Método para obtener todas las variantes activas
    public function getVariantesActivasAttribute()
    {
        if (!$this->es_aceite) {
            return collect();
        }

        return $this->aceites()->where('activo', true)->get();
    }

    // Método para obtener variantes con stock disponible
    public function getVariantesConStockAttribute()
    {
        if (!$this->es_aceite) {
            return collect();
        }

        return $this->aceites()
            ->where('activo', true)
            ->where('stock_disponible', '>', 0)
            ->get();
    }
}