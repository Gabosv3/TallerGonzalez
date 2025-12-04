<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ReporteProductos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.reporte-productos';
    protected static bool $shouldRegisterNavigation = false;

    public function getViewData(): array
    {
        return [];
    }
}
