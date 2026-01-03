<?php

namespace App\Exports;

use App\Models\Producto;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell\StringCell;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Color;

class ReordenExport
{
    public function export()
    {
        $writer = new Writer();
        $fileName = 'reorden-' . now()->format('Ymd-His') . '.xlsx';
        $filePath = storage_path('app/exports/' . $fileName);

        if (!is_dir(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0777, true);
        }

        $writer->openToFile($filePath);

        // Header style
        $headerStyle = (new Style())
            ->setBackgroundColor('DC2626')
            ->setFontColor(Color::WHITE)
            ->setFontBold();

        // Get products needing reorder
        $productos = Producto::with(['marca', 'tipoProducto'])
            ->where('stock_minimo', '>', 0)
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('marca_id')
            ->orderBy('stock_actual')
            ->get();

        // Header row
        $headerCells = [
            new StringCell('ID', $headerStyle),
            new StringCell('Código', $headerStyle),
            new StringCell('Nombre', $headerStyle),
            new StringCell('Marca', $headerStyle),
            new StringCell('Tipo', $headerStyle),
            new StringCell('Stock Actual', $headerStyle),
            new StringCell('Stock Mínimo', $headerStyle),
            new StringCell('Stock Máximo', $headerStyle),
            new StringCell('Faltante', $headerStyle),
            new StringCell('Precio Compra', $headerStyle),
            new StringCell('Precio Venta', $headerStyle),
            new StringCell('Precio + IVA', $headerStyle),
            new StringCell('Valor Total Faltante', $headerStyle)
        ];
        $writer->addRow(new Row($headerCells));

        // Data rows
        $totalFaltante = 0;
        $totalValor = 0;
        
        foreach ($productos as $producto) {
            $faltante = max(0, $producto->stock_minimo - $producto->stock_actual);
            $valor_faltante = $faltante * $producto->precio_compra;
            
            $totalFaltante += $faltante;
            $totalValor += $valor_faltante;

            $cells = [
                new StringCell((string)$producto->id, null),
                new StringCell($producto->codigo, null),
                new StringCell($producto->nombre, null),
                new StringCell($producto->marca?->nombre ?? 'N/A', null),
                new StringCell($producto->tipoProducto?->nombre ?? 'N/A', null),
                new StringCell((string)$producto->stock_actual, null),
                new StringCell((string)$producto->stock_minimo, null),
                new StringCell((string)$producto->stock_maximo, null),
                new StringCell((string)$faltante, null),
                new StringCell((string)$producto->precio_compra, null),
                new StringCell((string)$producto->precio_venta, null),
                new StringCell((string)round($producto->precio_venta * 1.13, 2), null),
                new StringCell((string)round($valor_faltante, 2), null)
            ];
            $writer->addRow(new Row($cells));
        }

        // Summary row
        $writer->addRow(new Row([new StringCell('', null)]));
        
        $summaryCells = [
            new StringCell('', null),
            new StringCell('', null),
            new StringCell('', null),
            new StringCell('', null),
            new StringCell('Total a Reabastecer:', null),
            new StringCell('', null),
            new StringCell('', null),
            new StringCell('', null),
            new StringCell((string)$totalFaltante, null),
            new StringCell('', null),
            new StringCell('', null),
            new StringCell('', null),
            new StringCell((string)round($totalValor, 2), null)
        ];
        $writer->addRow(new Row($summaryCells));

        $writer->close();

        return $filePath;
    }
}
