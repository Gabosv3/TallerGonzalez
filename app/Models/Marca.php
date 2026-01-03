<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Marca extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'marcas';

    protected $fillable = [
        'nombre',
        'logo',
        'pais_origen',
        'descripcion',
        'activo',
        'orden',
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
            'nombre' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'pais_origen' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
            'orden' => 'integer|min:0',
        ];
    }

    /**
     * Mensajes de validación en español
     */
    public static function validationMessages(): array
    {
        return [
            'nombre.required' => 'El nombre de la marca es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'nombre.unique' => 'Esta marca ya está registrada.',
            'logo.image' => 'El archivo debe ser una imagen válida.',
            'logo.max' => 'El logo no puede exceder 2MB.',
            'pais_origen.max' => 'El país de origen no puede exceder 100 caracteres.',
            'orden.integer' => 'El orden debe ser un número entero.',
            'orden.min' => 'El orden no puede ser negativo.',
            'activo.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    protected static $logAttributes = ['*'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // Relación con aceites
    public function aceites(): HasMany
    {
        return $this->hasMany(Aceite::class);
    }

    // Relación con productos
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    // Scope para marcas activas
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    // Scope ordenado
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    // Helper para obtener logo o placeholder
  
}