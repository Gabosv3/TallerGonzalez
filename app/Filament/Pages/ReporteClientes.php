<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ReporteClientes extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.reporte-clientes';
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view_reporte_clientes') ?? false;
    }

    public function getViewData(): array
    {
        return [];
    }
}
