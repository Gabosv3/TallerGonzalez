<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FacturaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'numero_factura' => $this->numero_factura,
            'cliente_id' => $this->cliente_id,
            'cliente' => $this->cliente,
            'fecha' => $this->fecha ? $this->fecha->toDateString() : null,
            'total' => (float) $this->total,
            'estado' => $this->estado,
            'detalles' => $this->whenLoaded('detalles', function () {
                return $this->detalles->map(function ($d) {
                    return [
                        'id' => $d->id,
                        'producto_id' => $d->producto_id,
                        'producto' => $d->producto?->nombre,
                        'cantidad' => (int) $d->cantidad,
                        'precio_unitario' => (float) $d->precio_unitario,
                        'subtotal' => (float) $d->subtotal,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'created_by' => $this->created_by,
            'creador' => $this->whenLoaded('creador', function () {
                return [
                    'id' => $this->creador->id,
                    'name' => $this->creador->name ?? $this->creador->email,
                    'email' => $this->creador->email ?? null,
                ];
            }),
        ];
    }
}
