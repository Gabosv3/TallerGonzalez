<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * Attributes to log for activity log.
     * Adjust array if you want specific attributes only.
     */
    protected static $logAttributes = ['*'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
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