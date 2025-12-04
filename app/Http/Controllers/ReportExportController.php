<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;

class ReportExportController extends Controller
{
    public function exportFacturasPDF(Request $request)
    {
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');
        $cliente_id = $request->query('cliente_id');
        
        $query = Factura::with('cliente', 'detalles')->latest();
        
        if ($desde) {
            $query->whereDate('created_at', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('created_at', '<=', $hasta);
        }
        if ($cliente_id) {
            $query->where('cliente_id', $cliente_id);
        }
        
        $facturas = $query->get();
        
        $html = view('exports.facturas-pdf', compact('facturas', 'desde', 'hasta'))->render();
        
        return $this->downloadPDF($html, 'reporte-facturas-' . date('Y-m-d-His'));
    }

    public function exportProductosPDF(Request $request)
    {
        $marca_id = $request->query('marca_id');
        $stock = $request->query('stock');
        
        $query = Producto::with('marca')->where('activo', 1);
        
        if ($marca_id) {
            $query->where('marca_id', $marca_id);
        }
        
        if ($stock === 'bajo') {
            $query->whereRaw('stock_actual < stock_minimo');
        } elseif ($stock === 'disponible') {
            $query->whereRaw('stock_actual >= stock_minimo');
        }
        
        $productos = $query->get();
        
        $html = view('exports.productos-pdf', compact('productos'))->render();
        
        return $this->downloadPDF($html, 'reporte-productos-' . date('Y-m-d-His'));
    }

    public function exportClientesPDF(Request $request)
    {
        $estado = $request->query('estado');
        
        $query = Cliente::withCount('facturas')->withSum('facturas', 'total');
        
        if ($estado !== null && $estado !== '') {
            $query->where('activo', (int)$estado);
        } else {
            $query->where('activo', 1);
        }
        
        $clientes = $query->get();
        
        $html = view('exports.clientes-pdf', compact('clientes'))->render();
        
        return $this->downloadPDF($html, 'reporte-clientes-' . date('Y-m-d-His'));
    }

    private function downloadPDF($html, $filename)
    {
        // Usar DomPDF si está disponible
        try {
            $pdf = \PDF::loadHTML($html);
            return $pdf->download($filename . '.pdf');
        } catch (\Exception $e) {
            // Fallback: generar HTML descargable
            return response($html, 200)
                ->header('Content-Type', 'text/html; charset=utf-8')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}.html\"");
        }
    }

    // Alias para Excel (redirigen a PDF)
    public function exportFacturasExcel(Request $request)
    {
        return $this->exportFacturasPDF($request);
    }

    public function exportProductosExcel(Request $request)
    {
        return $this->exportProductosPDF($request);
    }

    public function exportClientesExcel(Request $request)
    {
        return $this->exportClientesPDF($request);
    }
}



