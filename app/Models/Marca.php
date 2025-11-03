<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Marca extends Model
{
    use SoftDeletes;

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

    // Relación con aceites
    public function aceites(): HasMany
    {
        return $this->hasMany(Aceite::class);
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
    public function getLogoTipoAttribute(): string
    {
        return $this->logo ?? '/images/placeholder-marca.png';
    }
}