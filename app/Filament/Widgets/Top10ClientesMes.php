<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class Top10ClientesMes extends ChartWidget
{
    protected static ?string $heading = '👥 Top 10 Clientes Que Más Compran Este Mes';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $mesActual = Carbon::now();
        $inicioMes = $mesActual->copy()->startOfMonth();
        $finMes = $mesActual->copy()->endOfMonth();

        $clientes = DB::table('clientes')
            ->join('facturas', 'clientes.id', '=', 'facturas.cliente_id')
            ->whereBetween('facturas.created_at', [$inicioMes, $finMes])
            ->select(
                'clientes.nombre',
                DB::raw('COUNT(facturas.id) as total_facturas'),
                DB::raw('SUM(facturas.total) as total_gasto')
            )
            ->groupBy('clientes.id', 'clientes.nombre')
            ->orderBy('total_gasto', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Compras ($)',
                    'data' => $clientes->pluck('total_gasto')->toArray(),
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.5,
                ],
                [
                    'label' => 'Cantidad de Facturas',
                    'data' => $clientes->pluck('total_facturas')->toArray(),
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'tension' => 0.5,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $clientes->pluck('nombre')->toArray(),
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
