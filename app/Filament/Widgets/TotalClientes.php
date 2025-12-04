<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Cliente;

class TotalClientes extends Widget
{
    protected static string $view = 'filament.widgets.total-clientes';

    public int $count = 0;

    public function mount(): void
    {
        $this->count = Cliente::count();
    }
}
