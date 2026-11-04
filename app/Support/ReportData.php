<?php

namespace App\Support;

use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Builder;

/**
 * Consultas y resúmenes compartidos por el Centro de Reportes y las exportaciones PDF/Excel.
 */
class ReportData
{
    public static function facturas(array $f): Builder
    {
        return Factura::query()
            ->with('cliente')
            ->withCount('detalles')
            ->when($f['desde'] ?? null, fn ($q, $v) => $q->whereDate('fecha', '>=', $v))
            ->when($f['hasta'] ?? null, fn ($q, $v) => $q->whereDate('fecha', '<=', $v))
            ->when($f['cliente_id'] ?? null, fn ($q, $v) => $q->where('cliente_id', $v))
            ->when($f['estado'] ?? null, fn ($q, $v) => $q->where('estado', $v))
            ->orderByDesc('fecha')
            ->orderByDesc('id');
    }

    public static function facturasResumen(array $f): array
    {
        // Sin ORDER BY: MySQL en modo estricto rechaza ordenar una consulta de agregados
        $base = fn () => self::facturas($f)->reorder();
        $vigentes = $base()->where('estado', '!=', 'cancelada');
        $cantidad = (clone $vigentes)->count();
        $total = (float) (clone $vigentes)->sum('total');

        return [
            'cantidad' => $base()->count(),
            'total' => $total,
            'promedio' => $cantidad > 0 ? $total / $cantidad : 0.0,
            'canceladas' => $base()->where('estado', 'cancelada')->count(),
        ];
    }

    public static function productos(array $f): Builder
    {
        return Producto::query()
            ->with(['marca', 'tipoProducto'])
            ->where('activo', 1)
            ->when($f['marca_id'] ?? null, fn ($q, $v) => $q->where('marca_id', $v))
            ->when(($f['stock'] ?? null) === 'bajo', fn ($q) => $q->bajoStock())
            ->when(($f['stock'] ?? null) === 'agotado', fn ($q) => $q->where('stock_actual', '<=', 0))
            ->when(($f['stock'] ?? null) === 'disponible', fn ($q) => $q->whereColumn('stock_actual', '>', 'stock_minimo'))
            ->orderBy('nombre');
    }

    public static function productosResumen(array $f): array
    {
        $base = fn () => self::productos($f)->reorder();

        return [
            'cantidad' => $base()->count(),
            'unidades' => (int) $base()->sum('stock_actual'),
            'valor' => (float) $base()->selectRaw('COALESCE(SUM(stock_actual * precio_compra), 0) as v')->value('v'),
            'bajo' => $base()->bajoStock()->count(),
        ];
    }

    public static function clientes(array $f): Builder
    {
        $periodo = fn ($q) => $q->where('estado', '!=', 'cancelada')
            ->when($f['desde'] ?? null, fn ($q, $v) => $q->whereDate('fecha', '>=', $v))
            ->when($f['hasta'] ?? null, fn ($q, $v) => $q->whereDate('fecha', '<=', $v));

        $estado = $f['estado'] ?? '1';

        return Cliente::query()
            ->withCount(['facturas as facturas_count' => $periodo])
            ->withSum(['facturas as facturas_sum_total' => $periodo], 'total')
            ->when($estado !== 'todos' && $estado !== '', fn ($q) => $q->where('activo', (int) $estado))
            ->orderByDesc('facturas_sum_total')
            ->orderBy('nombre');
    }

    public static function clientesResumen(array $f): array
    {
        $clientes = self::clientes($f)->get();

        return [
            'cantidad' => $clientes->count(),
            'con_compras' => $clientes->where('facturas_count', '>', 0)->count(),
            'total' => (float) $clientes->sum('facturas_sum_total'),
        ];
    }

    public static function nombreCliente(?Cliente $c, ?string $respaldo = null): string
    {
        if (! $c) {
            return $respaldo ?: 'Consumidor final';
        }

        return trim($c->razon_social ?: $c->nombre_completo) ?: ($respaldo ?: 'Sin nombre');
    }
}
