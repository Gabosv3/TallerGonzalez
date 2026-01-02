<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Venta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Clientes', Cliente::count())
                ->description('Clientes registrados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Total Productos', Producto::count())
                ->description('Productos en inventario')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
            Stat::make('Total Pedidos', Pedido::count())
                ->description('Pedidos realizados')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),
        ];
    }
}
