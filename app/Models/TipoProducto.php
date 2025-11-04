<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoProducto extends Model
{
    use HasFactory;

    protected $table = 'tipos_producto';

    protected $fillable = [
        'nombre',
        'descripcion',
        'clase_modelo',
        'requiere_especificaciones',
        'activo'
    ];

    protected $casts = [
        'requiere_especificaciones' => 'boolean',
        'activo' => 'boolean'
    ];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    // Scope para tipos activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Scope para tipos que requieren especificaciones
    public function scopeConEspecificaciones($query)
    {
        return $query->where('requiere_especificaciones', true);
    }

    // Helper para saber si es aceite
    public function getEsAceiteAttribute(): bool
    {
        return $this->nombre === 'aceite';
    }
}
