<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Producto;

class TotalProductos extends Widget
{
    protected static string $view = 'filament.widgets.total-productos';

    public int $count = 0;

    public function mount(): void
    {
        $this->count = Producto::count();
    }
}
