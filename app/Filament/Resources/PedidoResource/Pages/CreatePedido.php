<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePedido extends CreateRecord
{
    protected static string $resource = PedidoResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remover campos temporales que no se deben guardar
        if (isset($data['detalles'])) {
            foreach ($data['detalles'] as &$detalle) {
                unset($detalle['precio_con_iva_temp']);
            }
        }
        return $data;
    }
}

