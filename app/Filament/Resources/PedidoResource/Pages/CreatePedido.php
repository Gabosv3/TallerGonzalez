<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePedido extends CreateRecord
{
    protected static string $resource = PedidoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return array_merge($data, PedidoResource::totalesDesdeFormulario($data));
    }

    protected function afterCreate(): void
    {
        // Los detalles se guardan después de la orden: se recalculan los totales con lo guardado
        $this->record->calcularTotales();
    }
}
