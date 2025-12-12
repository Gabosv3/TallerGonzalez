<?php

namespace App\Filament\Resources\ServicioResource\Pages;

use App\Filament\Resources\ServicioResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateServicio extends CreateRecord
{
    protected static string $resource = ServicioResource::class;

    // Oculta acciones para enfoque de auto-guardado al crear y redirigir a edición
    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['productos'] = $data['productos'] ?? [];
        $data['servicios'] = $data['servicios'] ?? [];
        
        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Registro creado')
            ->body('Se guardará automáticamente cualquier cambio.')
            ->success()
            ->duration(2000)
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}



