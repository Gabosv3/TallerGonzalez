<?php

namespace App\Filament\Resources\AceiteResource\Pages;

use App\Filament\Resources\AceiteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAceites extends ListRecords
{
    protected static string $resource = AceiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
