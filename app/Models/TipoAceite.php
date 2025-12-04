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