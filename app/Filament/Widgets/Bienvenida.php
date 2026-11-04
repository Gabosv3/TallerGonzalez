<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ReportesGenerales;
use App\Filament\Resources\ClienteResource;
use App\Filament\Resources\PedidoResource;
use App\Filament\Resources\ProductoResource;
use Filament\Widgets\Widget;

class Bienvenida extends Widget
{
    protected static string $view = 'filament.widgets.bienvenida';
    protected static ?int $sort = 0;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $ahora = now('America/El_Salvador');
        $saludo = match (true) {
            $ahora->hour < 12 => 'Buenos días',
            $ahora->hour < 19 => 'Buenas tardes',
            default => 'Buenas noches',
        };

        $acciones = [];
        foreach ([
            ['Nuevo pedido', 'heroicon-m-clipboard-document-list', fn () => PedidoResource::getUrl('create'), PedidoResource::class],
            ['Nuevo producto', 'heroicon-m-cube', fn () => ProductoResource::getUrl('create'), ProductoResource::class],
            ['Nuevo cliente', 'heroicon-m-user-plus', fn () => ClienteResource::getUrl('create'), ClienteResource::class],
            ['Reportes', 'heroicon-m-chart-bar', fn () => ReportesGenerales::getUrl(), ReportesGenerales::class],
        ] as [$label, $icon, $url, $class]) {
            if ($class::canAccess()) {
                $acciones[] = ['label' => $label, 'icon' => $icon, 'url' => $url()];
            }
        }

        return [
            'saludo' => $saludo,
            'nombre' => explode(' ', trim(auth()->user()->name))[0],
            'fecha' => ucfirst($ahora->locale('es')->translatedFormat('l, d \d\e F \d\e Y')),
            'acciones' => $acciones,
        ];
    }
}
