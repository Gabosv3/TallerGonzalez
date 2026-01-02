<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class PedidoReportController extends Controller
{
    public function reporte(Pedido $pedido)
    {
        try {
            $pdf = Pdf::loadView('reports.pedido', ['pedido' => $pedido->load(['proveedor', 'detalles.producto', 'detalles.aceite.marca', 'detalles.aceite.tipoAceite'])]);
            $fileName = 'pedido-' . $pedido->numero_factura . '-' . now()->format('Ymd') . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error generating pedido PDF: ' . $e->getMessage());
            return Response::make('Error generando el reporte de pedido', 500);
        }
    }

    public function multiple(Request $request)
    {
        try {
            $ids = [];
            if ($request->has('pedidos')) {
                $ids = is_array($request->query('pedidos')) ? $request->query('pedidos') : array_filter(explode(',', $request->query('pedidos')));
            }

            $query = Pedido::with(['proveedor', 'detalles.producto', 'detalles.aceite.marca', 'detalles.aceite.tipoAceite']);
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }

            $pedidos = $query->orderBy('fecha_orden', 'desc')->get();

            // Si piden CSV
            if ($request->query('format') === 'csv') {
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="pedidos_export_' . now()->format('Ymd_His') . '.csv"',
                ];

                $callback = function () use ($pedidos) {
                    $handle = fopen('php://output', 'w');
                    fputcsv($handle, ['ID', 'N° Orden', 'Proveedor', 'Fecha Orden', 'Estado', 'Total', 'Items']);

                    foreach ($pedidos as $p) {
                        fputcsv($handle, [
                            $p->id,
                            $p->numero_factura,
                            $p->proveedor?->nombre,
                            $p->fecha_orden?->format('Y-m-d'),
                            $p->estado,
                            $p->total,
                            $p->detalles->count(),
                        ]);
                    }

                    fclose($handle);
                };

                return Response::stream($callback, 200, $headers);
            }

            // PDF con multiples pedidos en un mismo documento
            $pdf = Pdf::loadView('reports.pedidos-multiple', ['pedidos' => $pedidos]);
            $fileName = 'pedidos-' . now()->format('Ymd_His') . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error generating multiple pedidos report: ' . $e->getMessage());
            return Response::make('Error generando el reporte de pedidos', 500);
        }
    }

    /**
     * Reporte de compras agrupado por producto
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function comprasPorProducto(Request $request)
    {
        try {
            $fechaInicio = $request->query('fecha_inicio');
            $fechaFin = $request->query('fecha_fin');

            // Obtener detalles de pedidos con relaciones
            $query = \App\Models\PedidoDetalle::with([
                'pedido.proveedor',
                'producto',
                'aceite.marca',
                'aceite.tipoAceite'
            ]);

            // Aplicar filtro de fechas si se proporcionan
            if ($fechaInicio) {
                $query->whereHas('pedido', fn($q) => $q->whereDate('fecha_orden', '>=', $fechaInicio));
            }
            if ($fechaFin) {
                $query->whereHas('pedido', fn($q) => $q->whereDate('fecha_orden', '<=', $fechaFin));
            }

            $detalles = $query->get();

            // Agrupar por producto principal (producto o aceite)
            $comprasPorProducto = [];
            
            foreach ($detalles as $detalle) {
                // Determinar el nombre del producto
                $nombreProducto = $detalle->producto_nombre ?? ($detalle->producto?->nombre ?? 'Sin nombre');
                $codigoProducto = $detalle->producto?->codigo ?? 'N/A';
                $inventarioActual = $detalle->producto?->stock_actual ?? 0;
                $productoId = $detalle->producto_id ?? 'aceite-' . $detalle->aceite_id;
                
                // Si no existe, crear el grupo de producto
                if (!isset($comprasPorProducto[$productoId])) {
                    $comprasPorProducto[$productoId] = [
                        'nombre' => $nombreProducto,
                        'codigo' => $codigoProducto,
                        'stock_actual' => $inventarioActual,
                        'compras' => []
                    ];
                }

                // Crear clave para agrupar por proveedor y factura
                $proveedorId = $detalle->pedido->proveedor_id;
                $numeroFactura = $detalle->pedido->numero_factura;
                $clave = $proveedorId . '-' . $numeroFactura;

                // Si no existe el grupo proveedor-factura, crearlo
                if (!isset($comprasPorProducto[$productoId]['compras'][$clave])) {
                    $comprasPorProducto[$productoId]['compras'][$clave] = [
                        'proveedor' => $detalle->pedido->proveedor->nombre,
                        'numero_factura' => $numeroFactura,
                        'fecha_orden' => $detalle->pedido->fecha_orden,
                        'precio_sin_iva' => $detalle->precio_sin_iva,
                        'precio_con_iva' => $detalle->precio_con_iva,
                        'cantidad_total' => 0,
                    ];
                }

                // Sumar la cantidad
                $comprasPorProducto[$productoId]['compras'][$clave]['cantidad_total'] += $detalle->cantidad;
            }

            // Preparar datos para la vista
            $datos = [
                'compras' => $comprasPorProducto,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'fecha_reporte' => now()->format('d/m/Y H:i')
            ];

            // Generar PDF
            $pdf = Pdf::loadView('reports.compras-por-producto', $datos);
            $fileName = 'compras-por-producto-' . now()->format('Ymd_His') . '.pdf';
            
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('Error generating compras por producto report: ' . $e->getMessage());
            return Response::make('Error generando el reporte de compras: ' . $e->getMessage(), 500);
        }
    }
}
