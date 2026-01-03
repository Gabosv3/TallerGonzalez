<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'codigo',
        'nombre',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'pais',
        'rfc',
        'notas',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Reglas de validación para el modelo
     */
    public static function validationRules(): array
    {
        return [
            'codigo' => 'nullable|string|max:50|unique:proveedores,codigo',
            'nombre' => 'required|string|max:255|unique:proveedores,nombre',
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:100',
            'rfc' => 'nullable|string|max:20',
            'notas' => 'nullable|string',
            'activo' => 'boolean',
        ];
    }

    /**
     * Mensajes de validación en español
     */
    public static function validationMessages(): array
    {
        return [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres.',
            'nombre.unique' => 'Este nombre de proveedor ya está registrado.',
            'codigo.max' => 'El código no puede exceder 50 caracteres.',
            'codigo.unique' => 'Este código de proveedor ya está registrado.',
            'contacto.max' => 'El contacto no puede exceder 100 caracteres.',
            'telefono.max' => 'El teléfono no puede exceder 20 caracteres.',
            'email.email' => 'El correo debe ser una dirección válida.',
            'email.max' => 'El correo no puede exceder 100 caracteres.',
            'direccion.max' => 'La dirección no puede exceder 255 caracteres.',
            'ciudad.max' => 'La ciudad no puede exceder 100 caracteres.',
            'pais.max' => 'El país no puede exceder 100 caracteres.',
            'rfc.max' => 'El RFC no puede exceder 20 caracteres.',
            'activo.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    /**
     * Relación: Un proveedor puede tener muchos productos
     */
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    /**
     * Relación: Un proveedor puede tener muchos pedidos
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Scope para filtrar proveedores activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para filtrar por ciudad
     */
    public function scopePorCiudad($query, $ciudad)
    {
        return $query->where('ciudad', $ciudad);
    }

    /**
     * Scope para filtrar por país
     */
    public function scopePorPais($query, $pais)
    {
        return $query->where('pais', $pais);
    }

    /**
     * Accessor para nombre completo con código
     */
    public function getNombreCompletoAttribute()
    {
        return $this->codigo ? "{$this->codigo} - {$this->nombre}" : $this->nombre;
    }
}