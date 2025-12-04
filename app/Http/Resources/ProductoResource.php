<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'nombre_completo' => $this->nombre_completo,
            'descripcion' => $this->descripcion,
            'Precio_unitario' => (float) $this->precio_venta,
            // Inventario
            'stock_actual' => $this->stock_actual,
            
           
        ];
    }
}