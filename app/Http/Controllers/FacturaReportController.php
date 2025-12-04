<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FacturaReportController extends Controller
{
    public function show(Factura $factura)
    {
        try {
            $factura->load('detalles.producto');
            $pdf = Pdf::loadView('reports.factura', ['factura' => $factura]);
            $fileName = 'factura-' . ($factura->numero_factura ?? $factura->id) . '-' . now()->format('Ymd') . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error generating factura PDF: ' . $e->getMessage());
            return Response::make('Error generando el reporte de factura', 500);
        }
    }

    public function periodo(Request $request)
    {
        try {
            $from = $request->query('from') ? Carbon::parse($request->query('from'))->startOfDay() : null;
            $to = $request->query('to') ? Carbon::parse($request->query('to'))->endOfDay() : null;

            $query = Factura::with('detalles.producto');
            if ($from && $to) {
                $query->whereBetween('fecha', [$from, $to]);
            } elseif ($from) {
                $query->where('fecha', '>=', $from);
            } elseif ($to) {
                $query->where('fecha', '<=', $to);
            }

            $facturas = $query->orderBy('fecha', 'desc')->get();

            // CSV export
            if ($request->query('format') === 'csv') {
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="facturas_export_' . now()->format('Ymd_His') . '.csv"',
                ];

                $callback = function () use ($facturas) {
                    $handle = fopen('php://output', 'w');
                    fputcsv($handle, ['ID', 'N° Factura', 'Cliente', 'Fecha', 'Total', 'Items']);

                    foreach ($facturas as $f) {
                        fputcsv($handle, [
                            $f->id,
                            $f->numero_factura,
                            $f->cliente,
                            optional($f->fecha)->format('Y-m-d'),
                            $f->total,
                            $f->detalles->count(),
                        ]);
                    }

                    fclose($handle);
                };

                return Response::stream($callback, 200, $headers);
            }

            // PDF
            $pdf = Pdf::loadView('reports.facturas-periodo', ['facturas' => $facturas, 'from' => $from, 'to' => $to]);
            $fileName = 'facturas_periodo_' . now()->format('Ymd_His') . '.pdf';
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error generating facturas periodo report: ' . $e->getMessage());
            return Response::make('Error generando el reporte de facturas', 500);
        }
    }
}
