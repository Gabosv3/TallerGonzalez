<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaEconomica extends Model
{
    use HasFactory;

    protected $table = 'categorias_economicas';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'descripcion',
    ];

    /**
     * Relación: Una categoría económica puede tener muchos clientes
     */
    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class, 'categoria_economica_codigo', 'codigo');
    }
}
