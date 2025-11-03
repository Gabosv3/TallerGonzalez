<?php

namespace App\Filament\Resources\TipoAceiteResource\Pages;

use App\Filament\Resources\TipoAceiteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoAceite extends EditRecord
{
    protected static string $resource = TipoAceiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
