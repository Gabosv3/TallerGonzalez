<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Pedido;
use Carbon\Carbon;

class PedidosPorMes extends Widget
{
    protected static string $view = 'filament.widgets.pedidos-por-mes';

    public array $labels = [];
    public array $data = [];

    public function mount(): void
    {
        $this->labels = [];
        $this->data = [];

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->format('M Y');
            $start = $date->copy()->startOfMonth()->toDateString();
            $end = $date->copy()->endOfMonth()->toDateString();

            $count = Pedido::whereBetween('created_at', [$start, $end])->count();

            $this->labels[] = $monthLabel;
            $this->data[] = $count;
        }
    }
}
