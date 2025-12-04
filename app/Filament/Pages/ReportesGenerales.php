<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ReportesGenerales extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reportes';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.reportes-generales';
    protected static bool $shouldRegisterNavigation = true;

    public function getViewData(): array
    {
        return [];
    }
}
