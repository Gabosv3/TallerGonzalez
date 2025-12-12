<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class ProductoReportController extends Controller
{
    public function reporte(Producto $producto)
    {
        try {
            $pdf = Pdf::loadView('reports.producto', ['producto' => $producto]);
            $fileName = 'producto-' . $producto->codigo . '-' . now()->format('Ymd') . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error generating product PDF: ' . $e->getMessage());
            return Response::make('Error generando el reporte', 500);
        }
    }

    public function reporteGeneral()
    {
        try {
            $products = Producto::with(['marca', 'tipoProducto'])->orderBy('id')->get();
            $pdf = Pdf::loadView('reports.productos-general', ['products' => $products]);
            $fileName = 'productos-general-' . now()->format('Ymd') . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error generating general products PDF: ' . $e->getMessage());
            return Response::make('Error generando el reporte general', 500);
        }
    }
    public function exportCsv(Request $request)
    {
        $idsParam = $request->query('ids');
        $query = Producto::query();

        if ($idsParam) {
            $ids = array_filter(explode(',', $idsParam));
            $query->whereIn('id', $ids);
        }

        $products = $query->with(['marca', 'tipoProducto'])->orderBy('id')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="productos_export_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($products) {
            $handle = fopen('php://output', 'w');
            // Header
            fputcsv($handle, ['ID', 'Código', 'Nombre', 'Tipo', 'Marca', 'Precio Venta', 'Precio + IVA', 'Precio Compra', 'Stock Actual', 'Activo']);

            foreach ($products as $p) {
                fputcsv($handle, [
                    $p->id,
                    $p->codigo,
                    $p->nombre,
                    $p->tipoProducto?->nombre,
                    $p->marca?->nombre,
                    $p->precio_venta,
                    round($p->precio_venta * 1.13, 2),
                    $p->precio_compra,
                    $p->stock_actual,
                    $p->activo ? 'Sí' : 'No',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function reorderReport(Request $request)
    {
        try {
            $query = Producto::with(['marca', 'tipoProducto'])
                ->where('stock_minimo', '>', 0)
                ->whereColumn('stock_actual', '<=', 'stock_minimo')
                ->orderBy('marca_id')
                ->orderBy('stock_actual');

            $products = $query->get();

            if ($request->query('format') === 'csv') {
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="reorder_' . now()->format('Ymd_His') . '.csv"',
                ];

                $callback = function () use ($products) {
                    $handle = fopen('php://output', 'w');
                    fputcsv($handle, ['ID', 'Código', 'Nombre', 'Marca', 'Stock Actual', 'Stock Mínimo', 'Faltante', 'Precio Venta', 'Precio + IVA']);

                    foreach ($products as $p) {
                        fputcsv($handle, [
                            $p->id,
                            $p->codigo,
                            $p->nombre,
                            $p->marca?->nombre,
                            $p->stock_actual,
                            $p->stock_minimo,
                            max(0, $p->stock_minimo - $p->stock_actual),
                            $p->precio_venta,
                            round($p->precio_venta * 1.13, 2),
                        ]);
                    }

                    fclose($handle);
                };

                return Response::stream($callback, 200, $headers);
            }

            $pdf = Pdf::loadView('reports.reorder', ['products' => $products]);
            $fileName = 'reorder_' . now()->format('Ymd') . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error generating reorder report: ' . $e->getMessage());
            return Response::make('Error generando el reporte de reorden', 500);
        }
    }
}
