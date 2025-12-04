<?php

namespace App\Filament\Widgets;

use App\Models\Producto;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ProductosMasVendidosTabla extends BaseWidget
{
    protected static ?string $heading = '📊 Productos Más Vendidos - Detalle';
    protected static ?int $sort = 4;

    protected function getTableQuery(): Builder
    {
        $mesActual = Carbon::now();
        $inicioMes = $mesActual->copy()->startOfMonth();
        $finMes = $mesActual->copy()->endOfMonth();

        return Producto::query()
            ->selectRaw('productos.id, productos.codigo, productos.nombre,
                SUM(detalle_facturas.cantidad) as total_vendido,
                SUM(detalle_facturas.subtotal) as total_ingresos,
                COUNT(DISTINCT facturas.id) as numero_facturas')
            ->join('detalle_facturas', 'productos.id', '=', 'detalle_facturas.producto_id')
            ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
            ->whereBetween('facturas.created_at', [$inicioMes, $finMes])
            ->groupBy('productos.id', 'productos.codigo', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('codigo')
                ->label('Código')
                ->sortable()
                ->searchable(),
            TextColumn::make('nombre')
                ->label('Producto')
                ->sortable()
                ->searchable()
                ->limit(30),
            BadgeColumn::make('total_vendido')
                ->label('Cantidad')
                ->color('info'),
            TextColumn::make('numero_facturas')
                ->label('Facturas')
                ->sortable(),
            TextColumn::make('total_ingresos')
                ->label('Ingresos')
                ->formatStateUsing(fn ($state) => '$' . number_format($state, 2))
                ->sortable(),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10];
    }
}
