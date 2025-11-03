<?php

namespace App\Filament\Resources\AceiteResource\Pages;

use App\Filament\Resources\AceiteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAceite extends EditRecord
{
    protected static string $resource = AceiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
