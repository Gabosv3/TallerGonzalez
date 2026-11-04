<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PedidoResource;
use App\Filament\Resources\ProductoResource;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Pedido;
use App\Models\Producto;
use Filament\Widgets\Widget;

class KpiCards extends Widget
{
    protected static string $view = 'filament.widgets.kpi-cards';
    protected static ?int $sort = 1;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $ventas = fn ($mes) => Factura::where('estado', '!=', 'cancelada')
            ->whereBetween('fecha', [$mes->copy()->startOfMonth(), $mes->copy()->endOfMonth()]);

        $ventasMes = (float) $ventas(now())->sum('total');
        $facturasMes = $ventas(now())->count();
        $ventasPrevias = (float) $ventas(now()->subMonthNoOverflow())->sum('total');
        $tendencia = $ventasPrevias > 0 ? (int) round((($ventasMes - $ventasPrevias) / $ventasPrevias) * 100) : null;

        $stockBajo = Producto::bajoStock()->count();
        $pendientes = Pedido::where('estado', 'pendiente')->count();
        $clientes = Cliente::count();

        return ['kpis' => [
            [
                'label' => 'Ventas del mes',
                'value' => '$' . number_format($ventasMes, 2),
                'foot' => $tendencia === null
                    ? $facturasMes . ' ' . ($facturasMes === 1 ? 'factura emitida' : 'facturas emitidas')
                    : 'vs. mes anterior · ' . $facturasMes . ' fact.',
                'trend' => $tendencia,
                'icon' => 'heroicon-o-banknotes',
                'color' => 'success',
                'url' => null,
            ],
            [
                'label' => 'Stock bajo',
                'value' => $stockBajo,
                'foot' => $stockBajo > 0 ? 'Requieren reabastecimiento' : 'Inventario al día',
                'trend' => null,
                'icon' => 'heroicon-o-exclamation-triangle',
                'color' => $stockBajo > 0 ? 'danger' : 'success',
                'url' => ProductoResource::getUrl('index'),
            ],
            [
                'label' => 'Pedidos pendientes',
                'value' => $pendientes,
                'foot' => ($total = Pedido::count()) . ($total === 1 ? ' pedido en total' : ' pedidos en total'),
                'trend' => null,
                'icon' => 'heroicon-o-clipboard-document-list',
                'color' => 'warning',
                'url' => PedidoResource::getUrl('index'),
            ],
            [
                'label' => 'Clientes registrados',
                'value' => $clientes,
                'foot' => Producto::count() . ' productos en catálogo',
                'trend' => null,
                'icon' => 'heroicon-o-user-group',
                'color' => 'info',
                'url' => null,
            ],
        ]];
    }
}
