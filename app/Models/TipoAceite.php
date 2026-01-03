<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TipoAceite extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'tipos_aceites';

    protected $fillable = [
        'nombre',
        'clave',
        'descripcion',
        'color',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Reglas de validación para el modelo
     */
    public static function validationRules(): array
    {
        return [
            'nombre' => 'required|string|max:255|unique:tipos_aceites,nombre',
            'clave' => 'required|string|max:50|unique:tipos_aceites,clave',
            'descripcion' => 'nullable|string',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-F]{6}$/i',
            'orden' => 'integer|min:0',
            'activo' => 'boolean',
        ];
    }

    /**
     * Mensajes de validación en español
     */
    public static function validationMessages(): array
    {
        return [
            'nombre.required' => 'El nombre del tipo de aceite es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'nombre.unique' => 'Este tipo de aceite ya está registrado.',
            'clave.required' => 'La clave es obligatoria.',
            'clave.max' => 'La clave no puede exceder 50 caracteres.',
            'clave.unique' => 'Esta clave ya está registrada.',
            'color.max' => 'El color debe ser un código hexadecimal válido.',
            'color.regex' => 'El color debe ser un código hexadecimal válido (ej: #6B7280).',
            'orden.integer' => 'El orden debe ser un número entero.',
            'orden.min' => 'El orden no puede ser negativo.',
            'activo.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    // Relación con aceites
    public function aceites(): HasMany
    {
        return $this->hasMany(Aceite::class);
    }

    // Scope para tipos activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Scope ordenado
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    // Helper para badge de color
    public function getBadgeColorAttribute(): string
    {
        return $this->color ?? '#6B7280';
    }

    protected static $logAttributes = ['*'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // Buscar por clave
    public static function findByClave(string $clave): ?self
    {
        return static::where('clave', $clave)->first();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}