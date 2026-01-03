<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Cliente extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'clientes';

    protected $fillable = [
        'codigo_cliente',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'telefono_alternativo',
        'dui',
        'nit',
        'nrc',
        'tipo_cliente',
        'razon_social',
        'nombre_comercial',
        'giro',
        'categoria_economica_codigo',
        'direccion',
        'departamento',
        'municipio',
        'distrito',
        'codigo_postal',
        'envio_direccion',
        'envio_departamento',
        'envio_municipio',
        'envio_distrito',
        'envio_referencia',
        'contacto_empresa',
        'cargo_contacto',
        'limite_credito',
        'dias_credito',
        'descuento_autorizado',
        'activo',
        'credito_activo',
        'observaciones',
        'aprobado_credito_at',
        'aprobado_por',
    ];

    protected $casts = [
        'limite_credito' => 'decimal:2',
        'descuento_autorizado' => 'decimal:2',
        'activo' => 'boolean',
        'credito_activo' => 'boolean',
        'aprobado_credito_at' => 'datetime',
        'dias_credito' => 'integer',
    ];

    /**
     * Reglas de validación para el modelo
     */
    public static function validationRules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|max:80',
            'telefono' => 'required|string|max:9',
            'telefono_alternativo' => 'nullable|string|max:9',
            'dui' => 'nullable|string|max:10',
            'nit' => 'nullable|string|max:14',
            'nrc' => 'nullable|string|max:10',
            'tipo_cliente' => 'required|in:natural,juridico',
            'direccion' => 'required|string|max:255',
            'departamento' => 'required|string|max:50',
            'municipio' => 'required|string|max:50',
            'limite_credito' => 'numeric|min:0',
            'dias_credito' => 'integer|min:0|max:365',
            'descuento_autorizado' => 'numeric|min:0|max:100',
            'activo' => 'boolean',
            'credito_activo' => 'boolean',
        ];
    }

    /**
     * Mensajes de validación en español
     */
    public static function validationMessages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.max' => 'El apellido no puede exceder 100 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo debe ser una dirección válida.',
            'email.max' => 'El correo no puede exceder 80 caracteres.',
            'email.unique' => 'Este correo ya está registrado en el sistema.',
            'telefono.required' => 'El teléfono principal es obligatorio.',
            'telefono.max' => 'El teléfono no puede exceder 9 caracteres.',
            'tipo_cliente.required' => 'El tipo de cliente es obligatorio.',
            'tipo_cliente.in' => 'El tipo de cliente debe ser natural o jurídico.',
            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.max' => 'La dirección no puede exceder 255 caracteres.',
            'departamento.required' => 'El departamento es obligatorio.',
            'municipio.required' => 'El municipio es obligatorio.',
            'limite_credito.numeric' => 'El límite de crédito debe ser un número.',
            'limite_credito.min' => 'El límite de crédito no puede ser negativo.',
            'dias_credito.integer' => 'Los días de crédito deben ser un número entero.',
            'dias_credito.min' => 'Los días de crédito no pueden ser negativos.',
            'dias_credito.max' => 'Los días de crédito no pueden exceder 365.',
            'descuento_autorizado.numeric' => 'El descuento debe ser un número.',
            'descuento_autorizado.min' => 'El descuento no puede ser menor a 0%.',
            'descuento_autorizado.max' => 'El descuento no puede ser mayor a 100%.',
        ];
    }

    /**
     * Attributes to log for activity log.
     * Adjust array if you want specific attributes only.
     */
    protected static $logAttributes = ['*'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     * Relación: Un cliente pertenece a una categoría económica
     */
    public function categoriaEconomica(): BelongsTo
    {
        return $this->belongsTo(CategoriaEconomica::class, 'categoria_economica_codigo', 'codigo');
    }

    /**
     * Relación: Un cliente puede tener muchas facturas
     */
    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class);
   
    }

    /**
     * Relación: Un cliente puede tener muchas ventas
     */
    /**public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Relación: Un cliente puede tener muchas cotizaciones
     */
    /*public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }

    /**
     * Relación con el usuario que aprobó el crédito
     */
    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    /**
     * Accessor: Nombre completo
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /**
     * Accessor: Dirección completa
     */
    public function getDireccionCompletaAttribute(): string
    {
        $parts = array_filter([
            $this->direccion,
            $this->distrito,
            $this->municipio,
            $this->departamento,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Accessor: Dirección de envío completa
     */
    public function getEnvioDireccionCompletaAttribute(): string
    {
        if (!$this->envio_direccion) {
            return $this->direccion_completa;
        }

        $parts = array_filter([
            $this->envio_direccion,
            $this->envio_distrito,
            $this->envio_municipio,
            $this->envio_departamento,
            $this->envio_referencia ? "Ref: {$this->envio_referencia}" : null,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Accessor: Información fiscal completa
     */
    public function getInfoFiscalAttribute(): string
    {
        $parts = [];
        if ($this->dui) $parts[] = "DUI: {$this->dui}";
        if ($this->nit) $parts[] = "NIT: {$this->nit}";
        if ($this->nrc) $parts[] = "NRC: {$this->nrc}";
        
        return implode(' | ', $parts);
    }

    /**
     * Verificar si es contribuyente (para crédito fiscal)
     */
    public function getEsContribuyenteAttribute(): bool
    {
        return in_array($this->tipo_cliente, ['contribuyente', 'empresa', 'distribuidor', 'mayorista']);
    }

    /**
     * Verificar si tiene crédito disponible
     */
    public function getTieneCreditoDisponibleAttribute(): bool
    {
        return $this->credito_activo && $this->limite_credito > 0;
    }

    /**
     * Scopes
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConCredito($query)
    {
        return $query->where('credito_activo', true);
    }

    public function scopeContribuyentes($query)
    {
        return $query->whereIn('tipo_cliente', ['contribuyente', 'empresa', 'distribuidor', 'mayorista']);
    }

    public function scopePorDepartamento($query, $departamento)
    {
        return $query->where('departamento', $departamento);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_cliente', $tipo);
    }

    /**
     * Generar código de cliente automáticamente
     */
    public static function generarCodigoCliente(): string
    {
        $ultimoCliente = static::withTrashed()->orderBy('id', 'desc')->first();
        $numero = $ultimoCliente ? intval(substr($ultimoCliente->codigo_cliente, 3)) + 1 : 1;
        
        return 'CLI' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cliente) {
            if (!$cliente->codigo_cliente) {
                $cliente->codigo_cliente = static::generarCodigoCliente();
            }
        });
    }
}