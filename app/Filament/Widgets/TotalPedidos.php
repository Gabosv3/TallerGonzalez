<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Pedido;

class TotalPedidos extends Widget
{
    protected static string $view = 'filament.widgets.total-pedidos';

    public int $count = 0;

    public function mount(): void
    {
        $this->count = Pedido::count();
    }
}
