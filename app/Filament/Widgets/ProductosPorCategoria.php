<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Marca;

class ProductosPorCategoria extends Widget
{
    protected static string $view = 'filament.widgets.productos-por-categoria';

    public array $labels = [];
    public array $data = [];
    public bool $hasData = false;

    public function mount(): void
    {
        $this->labels = [];
        $this->data = [];

        // Obtener marcas con productos usando query builder
        $marcas = Marca::query()
            ->withCount('productos')
            ->having('productos_count', '>', 0)
            ->orderBy('productos_count', 'desc')
            ->get();

        if ($marcas->isEmpty()) {
            $this->hasData = false;
            return;
        }

        foreach ($marcas as $marca) {
            $this->labels[] = $marca->nombre ?? 'Sin marca';
            $this->data[] = $marca->productos_count;
            $this->hasData = true;
        }
    }
}
