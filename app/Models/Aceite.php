<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Aceite extends Model
{
    use LogsActivity;

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
        'aplicaciones',
        'compatibilidad',
        'stock_disponible',
        'stock_minimo',
        'stock_maximo',
        'activo',
    ];

    protected $casts = [
        'capacidad_ml' => 'decimal:2',
        'aplicaciones' => 'array',
        'stock_disponible' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Atributos con valores por defecto
     */
    protected $attributes = [
        'stock_disponible' => 0,
        'stock_minimo' => 0,
        'stock_maximo' => null,
        'activo' => true,
        'unidad_medida' => 'ml',
    ];

    /**
     * Reglas de validación para el modelo
     */
    public static function validationRules(): array
    {
        return [
            'producto_id' => 'required|exists:productos,id',
            'marca_id' => 'required|exists:marcas,id',
            'viscosidad' => 'required|string|max:50',
            'tipo_aceite_id' => 'required|exists:tipos_aceites,id',
            'capacidad_ml' => 'required|numeric|min:0.01|max:999999.99',
            'modelo' => 'nullable|string|max:255',
            'presentacion' => 'nullable|string|max:100',
            'compatibilidad' => 'nullable|string',
            'stock_disponible' => 'integer|min:0',
            'stock_minimo' => 'integer|min:0',
            'stock_maximo' => 'nullable|integer|min:0',
            'activo' => 'boolean',
        ];
    }

    /**
     * Mensajes de validación en español
     */
    public static function validationMessages(): array
    {
        return [
            'producto_id.required' => 'Debes seleccionar un producto asociado.',
            'producto_id.exists' => 'El producto seleccionado no existe.',
            'marca_id.required' => 'Debes seleccionar una marca.',
            'marca_id.exists' => 'La marca seleccionada no existe.',
            'viscosidad.required' => 'La viscosidad es obligatoria.',
            'viscosidad.max' => 'La viscosidad no puede exceder 50 caracteres.',
            'tipo_aceite_id.required' => 'Debes seleccionar un tipo de aceite.',
            'tipo_aceite_id.exists' => 'El tipo de aceite seleccionado no existe.',
            'capacidad_ml.required' => 'La capacidad es obligatoria.',
            'capacidad_ml.numeric' => 'La capacidad debe ser un número.',
            'capacidad_ml.min' => 'La capacidad debe ser mayor a 0.',
            'stock_disponible.integer' => 'El stock debe ser un número entero.',
            'stock_disponible.min' => 'El stock no puede ser negativo.',
            'stock_minimo.integer' => 'El stock mínimo debe ser un número entero.',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',
            'stock_maximo.integer' => 'El stock máximo debe ser un número entero.',
            'stock_maximo.min' => 'El stock máximo no puede ser negativo.',
            'activo.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    protected static $logAttributes = ['*'];

    /**
     * Configure activity log options for this model (Spatie Activitylog v4+)
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

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
        $marcaNombre = $this->marca->nombre ?? 'Sin Marca';
        return "{$marcaNombre} {$this->viscosidad} {$this->capacidad_formateada}";
    }

    // Helper para nombre técnico completo
    public function getNombreTecnicoCompletoAttribute(): string
    {
        $marcaNombre = $this->marca->nombre ?? 'Sin Marca';
        $tipoAceite = $this->tipoAceite->nombre ?? '';
        $productoNombre = $this->producto->nombre ?? '';
        
        return "{$productoNombre} - {$marcaNombre} {$this->viscosidad} {$tipoAceite} {$this->capacidad_formateada}";
    }

    // Sincronizar stock con producto principal
    public function sincronizarStock(): void
    {
        if ($this->producto) {
            $this->producto->update([
                'stock_actual' => $this->stock_disponible
            ]);
        }
    }

    // Evento para auto-sincronizar stock
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($aceite) {
            $aceite->sincronizarStock();
        });
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

    // Estado de stock
    public function getEstadoStockAttribute(): string
    {
        if ($this->stock_disponible <= 0) {
            return 'agotado';
        } elseif ($this->stock_disponible <= $this->stock_minimo) {
            return 'bajo';
        } else {
            return 'disponible';
        }
    }
}