<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class Top10ProductosMes extends ChartWidget
{
    protected static ?string $heading = '🏆 Top 10 Productos Más Vendidos Este Mes';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $mesActual = Carbon::now();
        $inicioMes = $mesActual->copy()->startOfMonth();
        $finMes = $mesActual->copy()->endOfMonth();

        $productos = DB::table('detalle_facturas')
            ->join('productos', 'detalle_facturas.producto_id', '=', 'productos.id')
            ->join('facturas', 'detalle_facturas.factura_id', '=', 'facturas.id')
            ->whereBetween('facturas.created_at', [$inicioMes, $finMes])
            ->select(
                'productos.nombre',
                DB::raw('SUM(detalle_facturas.cantidad) as total_vendido'),
                DB::raw('SUM(detalle_facturas.subtotal) as total_ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad Vendida',
                    'data' => $productos->pluck('total_vendido')->toArray(),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.5,
                ],
                [
                    'label' => 'Ingresos ($)',
                    'data' => $productos->pluck('total_ingresos')->toArray(),
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.5,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $productos->pluck('nombre')->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'scales' => [
                'y' => [
                    'stacked' => false,
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'stacked' => false,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
