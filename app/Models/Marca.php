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