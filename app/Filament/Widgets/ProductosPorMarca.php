<?php

namespace App\Filament\Widgets;

use App\Models\Marca;
use Filament\Widgets\ChartWidget;

class ProductosPorMarca extends ChartWidget
{
    protected static ?string $heading = '📦 Productos por Marca';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 1;
    protected static string $type = 'doughnut';

    protected function getData(): array
    {
        $marcas = Marca::query()
            ->withCount('productos')
            ->having('productos_count', '>', 0)
            ->orderBy('productos_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Productos',
                    'data' => $marcas->pluck('productos_count')->toArray(),
                    'backgroundColor' => [
                        '#60A5FA', '#34D399', '#FBBF24', '#F472B6', '#A78BFA',
                        '#F87171', '#94A3B8', '#FB923C', '#EC4899', '#06B6D4'
                    ],
                ],
            ],
            'labels' => $marcas->pluck('nombre')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
