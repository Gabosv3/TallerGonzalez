<?php

namespace App\Http\Controllers;

use App\Exports\TablaExport;
use App\Models\Cliente;
use App\Models\Marca;
use App\Support\ReportData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReportExportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:view_reporte')];
    }

    // ── Facturas ─────────────────────────────────────────────

    public function exportFacturasPDF(Request $request)
    {
        $f = $this->filtros($request, ['desde', 'hasta', 'cliente_id', 'estado']);
        $cliente = ! empty($f['cliente_id']) ? Cliente::find($f['cliente_id']) : null;

        return $this->pdf('exports.facturas-pdf', 'reporte-facturas', [
            'facturas' => ReportData::facturas($f)->get(),
            'resumen' => ReportData::facturasResumen($f),
            'desde' => $f['desde'] ?? null,
            'hasta' => $f['hasta'] ?? null,
            'filtroCliente' => $cliente ? ReportData::nombreCliente($cliente) : null,
            'filtroEstado' => $f['estado'] ?? null,
        ]);
    }

    public function exportFacturasExcel(Request $request)
    {
        $f = $this->filtros($request, ['desde', 'hasta', 'cliente_id', 'estado']);

        $filas = ReportData::facturas($f)->lazy(500)->map(fn ($x) => [
            $x->numero_factura,
            ($x->fecha ?? $x->created_at)->format('d/m/Y'),
            ReportData::nombreCliente($x->getRelation('cliente'), $x->getRawOriginal('cliente')),
            (int) $x->detalles_count,
            ucfirst((string) $x->estado),
            (float) $x->total,
        ]);

        return $this->excel('facturas', ['N.º factura', 'Fecha', 'Cliente', 'Ítems', 'Estado', 'Total'], $filas, [18, 14, 36, 8, 14, 14], [5]);
    }

    // ── Inventario ───────────────────────────────────────────

    public function exportProductosPDF(Request $request)
    {
        $f = $this->filtros($request, ['marca_id', 'stock']);

        return $this->pdf('exports.productos-pdf', 'reporte-inventario', [
            'productos' => ReportData::productos($f)->get(),
            'resumen' => ReportData::productosResumen($f),
            'filtroMarca' => ! empty($f['marca_id']) ? Marca::find($f['marca_id'])?->nombre : null,
            'filtroStock' => ['bajo' => 'Bajo mínimo', 'agotado' => 'Agotado', 'disponible' => 'Sobre el mínimo'][$f['stock'] ?? ''] ?? null,
        ], 'landscape');
    }

    public function exportProductosExcel(Request $request)
    {
        $f = $this->filtros($request, ['marca_id', 'stock']);

        $filas = ReportData::productos($f)->lazy(500)->map(fn ($p) => [
            $p->codigo,
            $p->nombre,
            $p->marca?->nombre ?? '',
            $p->tipoProducto?->nombre ?? '',
            (int) $p->stock_actual,
            (int) $p->stock_minimo,
            $p->stock_actual <= 0 ? 'Agotado' : ($p->stock_actual <= $p->stock_minimo ? 'Bajo' : 'Normal'),
            (float) $p->precio_compra,
            (float) $p->precio_venta,
        ]);

        return $this->excel('inventario', ['Código', 'Producto', 'Marca', 'Tipo', 'Stock', 'Mínimo', 'Estado', 'P. compra', 'P. venta'], $filas, [18, 40, 18, 16, 8, 8, 12, 12, 12], [7, 8]);
    }

    // ── Clientes ─────────────────────────────────────────────

    public function exportClientesPDF(Request $request)
    {
        $f = $this->filtros($request, ['estado', 'desde', 'hasta']);

        return $this->pdf('exports.clientes-pdf', 'reporte-clientes', [
            'clientes' => ReportData::clientes($f)->get(),
            'resumen' => ReportData::clientesResumen($f),
            'desde' => $f['desde'] ?? null,
            'hasta' => $f['hasta'] ?? null,
            'filtroEstado' => match ($f['estado'] ?? '1') {
                '0' => 'Inactivos',
                'todos' => 'Todos',
                default => 'Activos',
            },
        ]);
    }

    public function exportClientesExcel(Request $request)
    {
        $f = $this->filtros($request, ['estado', 'desde', 'hasta']);

        $filas = ReportData::clientes($f)->lazy(500)->map(fn ($c) => [
            ReportData::nombreCliente($c),
            (string) $c->email,
            (string) $c->telefono,
            (string) ($c->dui ?: $c->nit),
            $c->activo ? 'Activo' : 'Inactivo',
            (int) $c->facturas_count,
            (float) ($c->facturas_sum_total ?? 0),
        ]);

        return $this->excel('clientes', ['Cliente', 'Correo', 'Teléfono', 'DUI / NIT', 'Estado', 'Facturas', 'Total comprado'], $filas, [34, 30, 16, 18, 12, 10, 16], [6]);
    }

    // ── Utilidades ───────────────────────────────────────────

    private function filtros(Request $request, array $permitidos): array
    {
        return array_filter(
            $request->only($permitidos),
            fn ($v) => $v !== null && $v !== ''
        );
    }

    private function pdf(string $vista, string $nombre, array $datos, string $orientacion = 'portrait')
    {
        return Pdf::loadView($vista, $datos)
            ->setPaper('a4', $orientacion)
            ->download($nombre . '-' . now()->format('Y-m-d-His') . '.pdf');
    }

    private function excel(string $nombre, array $encabezados, iterable $filas, array $anchos, array $moneda)
    {
        $ruta = TablaExport::generar($nombre, $encabezados, $filas, $anchos, $moneda);

        return response()->download($ruta, $nombre . '-' . now()->format('Y-m-d-His') . '.xlsx')->deleteFileAfterSend();
    }
}
