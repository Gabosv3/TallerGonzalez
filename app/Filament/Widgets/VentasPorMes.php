<?php

namespace App\Filament\Widgets;

use App\Models\Factura;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class VentasPorMes extends ChartWidget
{
    protected static ?string $heading = 'Ventas por mes (últimos 6 meses)';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;
    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');
            $data[] = (float) Factura::where('estado', '!=', 'cancelada')
                ->whereBetween('fecha', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->sum('total');
        }

        return [
            'datasets' => [[
                'label' => 'Ventas ($)',
                'data' => $data,
                'backgroundColor' => 'rgba(34, 197, 94, 0.65)',
                'borderRadius' => 6,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true]],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
