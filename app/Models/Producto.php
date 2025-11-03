<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'categoria_id', 'nombre', 'descripcion', 'precio_compra', 
        'precio_venta', 'stock', 'unidad_medida', 'codigo_inventario', 'imagen'
    ];

     public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function aceite()
    {
        return $this->hasOne(Aceite::class);
    }

    
}
