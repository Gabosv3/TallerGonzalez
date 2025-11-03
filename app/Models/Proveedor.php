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