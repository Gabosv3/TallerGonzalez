<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ReporteFacturas extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Facturas';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.reporte-facturas';
    protected static bool $shouldRegisterNavigation = false;

    public function getViewData(): array
    {
        return [];
    }
}
