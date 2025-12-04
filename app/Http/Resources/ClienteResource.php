<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo_cliente' => $this->codigo_cliente,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'telefono_alternativo' => $this->telefono_alternativo,
            'tipo_cliente' => $this->tipo_cliente,
            'razon_social' => $this->razon_social,
            'nombre_comercial' => $this->nombre_comercial,
            'giro' => $this->giro,
            'direccion' => $this->direccion,
            'departamento' => $this->departamento,
            'municipio' => $this->municipio,
            'distrito' => $this->distrito,
            'activo' => $this->activo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
