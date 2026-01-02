<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PedidosPorMes extends ChartWidget
{
    protected static ?string $heading = '📈 Pedidos por Mes (Últimos 6 meses)';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->translatedFormat('M Y');
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $count = Pedido::whereBetween('created_at', [$start, $end])->count();

            $labels[] = $monthLabel;
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pedidos',
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
