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
}
